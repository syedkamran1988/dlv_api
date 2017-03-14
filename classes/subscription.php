<?php

class Subscription extends Common {

    function __construct() {
        parent::__construct();
    }

    function create() {

        if (!empty($this->post['token']) &&
            !empty($this->post['plan_id']) &&
            !empty($this->post['email']) &&
            !empty($this->post['user_id'])) {

            $ex = $this->db->query("select * from plan where plan_id='" . $this->post['plan_id'] . "' and plan_delete='0' ");
            if ($row = $this->db->fetch()) {

                $input = array(
                    "source" => $this->post['token'],
                    "plan" => $row['plan_number'],
                    "email" => $this->post['email']
                );

                $customer = \Stripe\Customer::create($input);

                $customer_data = array(
                    "plan_id" => $this->post['plan_id'],
                    "customer_number" => $customer->id,
                    "customer_user_id" => $this->post['user_id'],
                    "customer_email" => $this->post['email'],
                    "customer_source" => $customer->default_source,
                    "customer_createdat" => date('Y-m-d H:i:s'),
                    "customer_updatedat" => date('Y-m-d H:i:s')
                );
                $rs_customer = $this->db->insert("customer", $customer_data);
                $customer_id = $this->db->insert_id();

                $subscription_data = array(
                    "plan_id" => $this->post['plan_id'],
                    "customer_id" => $customer_id,
                    "subscription_number" => $customer->subscriptions->data[0]->id,
                    "subscription_trial_start" => date('Y-m-d H:i:s', $customer->subscriptions->data[0]->trial_start),
                    "subscription_trial_end" => date('Y-m-d H:i:s', $customer->subscriptions->data[0]->trial_end),
                    "subscription_period_start" => date('Y-m-d H:i:s', $customer->subscriptions->data[0]->current_period_start),
                    "subscription_period_end" => date('Y-m-d H:i:s', $customer->subscriptions->data[0]->current_period_end),
                    "subscription_cancel_at_period_end" => '0',
                    "subscription_status" => $customer->subscriptions->data[0]->status,
                    "subscription_createdat" => date('Y-m-d H:i:s'),
                    "subscription_updatedat" => date('Y-m-d H:i:s')
                );
                $rs_subscription = $this->db->insert("subscription", $subscription_data);
                $this->dieSuccess("Subscription created successfully.");
            } else {
                $this->dieError('Invalid Plan Id.');
            }
        } else {
            $this->dieError('Some required filed(s) are missing.');
        }
    }

    function retrive() {

        if (!empty($this->post['subscription_id'])) {
            $output = array();
            $ex = $this->db->query("select * from subscription where subscription_id='" . $this->post['subscription_id'] . "' and subscription_delete='0' ");
            if ($row = $this->db->fetch()) {
                $output['row'] = $row;
                $this->dieSuccess("Subscription", $output);
            }
        } else {
            $this->dieError('Plan id is missing.');
        }
    }

    function cancel() {

        if (!empty($this->post['subscription_id'])) {
            
            $ex = $this->db->query("select * from subscription where subscription_id='" . $this->post['subscription_id'] . "' and subscription_delete='0' ");
            if ($row = $this->db->fetch()) {
                $subscription = \Stripe\Subscription::retrieve($row['subscription_number']);
            
            if(isset($this->post['at_period_end']) && $this->post['at_period_end'] == '1'){
            $subscription->cancel(array('at_period_end' => true));
                $subscription_data = array(
                    "subscription_cancel_at_period_end" => '1',
                    "subscription_updatedat" => date('Y-m-d H:i:s')
                );
                
            }else{
            $subscription->cancel();
                $subscription_data = array(
                    "subscription_status" => 'canceled',
                    "subscription_updatedat" => date('Y-m-d H:i:s')
                );                
            }
                $rs_subscription = $this->db->update("subscription", $subscription_data, "subscription_id", $this->post['subscription_id']);
            
            
                $this->dieSuccess("Subscription canceled successfully.");
            }
            
            
        } else {
            $this->dieError('Plan id is missing.');
        }
    }
}
?>
