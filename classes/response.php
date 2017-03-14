<?php

class Response extends Common {

    function __construct() {
        parent::__construct();
    }

    function event() {

        $input = @file_get_contents("php://input");
        $event_json = json_decode($input);
        $event = \Stripe\Event::retrieve($event_json->id);

        if ($event->type == 'customer.deleted') {

            /*
              $event->id;                        //  Event ID
              $event->created;                   //  Event Timestamp
              $event->data->object->id;          //  Customer ID
              $event->data->object->email;       //  Customer Email
             */
            if (isset($event->data->object->id)) {

                $ex = $this->db->query("select * from customer where customer_number='" . $event->data->object->id . "' and customer_delete='0' ");
                if ($row = $this->db->fetch()) {

                    $customer_data = array(
                        "customer_delete" => '1',
                        "customer_updatedat" => date('Y-m-d H:i:s')
                    );
                    $rs_subscription = $this->db->update("customer", $customer_data, 'customer_id', $row->customer_id);
                }
            }
        }

        if ($event->type == 'invoice.payment_succeeded') {

            /*
              $event->created;                     //  Event Timestamp
              $event->data->object->id;            //  Invoice ID
              $event->data->object->total;         //  Invoice Amount
              $event->data->object->paid;          //  true/false
              $event->data->object->subscription;  //  Subscription ID
              $event->data->object->customer;      //  Customer ID
             */
            if (isset($event->data->object->id)) {

                $ex = $this->db->query("select * from subscription where subscription_number='" . $event->data->object->subscription . "' and subscription_delete='0' ");
                if ($row = $this->db->fetch()) {

                    $subscription = \Stripe\Subscription::retrieve($event->data->object->subscription);

                    $subscription_data = array(
                        "subscription_trial_start" => date('Y-m-d H:i:s', $subscription->trial_start),
                        "subscription_trial_end" => date('Y-m-d H:i:s', $subscription->trial_start),
                        "subscription_period_start" => date('Y-m-d H:i:s', $subscription->current_period_start),
                        "subscription_period_end" => date('Y-m-d H:i:s', $subscription->current_period_end),
                        "subscription_cancel_at_period_end" => '0',
                        "subscription_status" => $subscription->status,
                        "subscription_updatedat" => date('Y-m-d H:i:s')
                    );

                    if ($subscription->cancel_at_period_end == true) {
                        $subscription_data["subscription_cancel_at_period_end"] = '1';
                    }

                    $rs_subscription = $this->db->update("subscription", $subscription_data, 'subscription_id', $row->subscription_id);
                }
            }
        }

        if ($event->type == 'invoice.payment_failed') {

            /*
              $event->created;                     //  Event Timestamp
              $event->data->object->id;            //  Invoice ID
              $event->data->object->total;         //  Invoice Amount
              $event->data->object->paid;          //  true/false
              $event->data->object->subscription;  //  Subscription ID
              $event->data->object->customer;      //  Customer ID
             */
            if (isset($event->data->object->id)) {

                $ex = $this->db->query("select * from subscription where subscription_number='" . $event->data->object->subscription . "' and subscription_delete='0' ");
                if ($row = $this->db->fetch()) {

                    $subscription = \Stripe\Subscription::retrieve($event->data->object->subscription);

                    $subscription_data = array(
                        "subscription_trial_start" => date('Y-m-d H:i:s', $subscription->trial_start),
                        "subscription_trial_end" => date('Y-m-d H:i:s', $subscription->trial_start),
                        "subscription_period_start" => date('Y-m-d H:i:s', $subscription->current_period_start),
                        "subscription_period_end" => date('Y-m-d H:i:s', $subscription->current_period_end),
                        "subscription_cancel_at_period_end" => '0',
                        "subscription_status" => $subscription->status,
                        "subscription_updatedat" => date('Y-m-d H:i:s')
                    );

                    if ($subscription->cancel_at_period_end == true) {
                        $subscription_data["subscription_cancel_at_period_end"] = '1';
                    }

                    $rs_subscription = $this->db->update("subscription", $subscription_data, 'subscription_id', $row->subscription_id);
                }
            }
        }

        if ($event->type == 'customer.subscription.deleted') {

            /*
              $event->id;                       //  Event ID
              $event->created;                  //  Event Timestamp
              $event->data->object->id;         //  Subscription ID
              $event->data->object->customer;   //  Customer ID
              $event->data->object->created;    //  Subscription Timestamp
              $event->data->object->current_period_end;      //  Timestamp
              $event->data->object->current_period_start;    //  Timestamp
              $event->data->object->status;                  //  trialing, active, past_due, canceled, unpaid
             */
            if (isset($event->data->object->id)) {

                $ex = $this->db->query("select * from subscription where subscription_number='" . $event->data->object->subscription . "' and subscription_delete='0' ");
                if ($row = $this->db->fetch()) {

                    $subscription = \Stripe\Subscription::retrieve($event->data->object->subscription);

                    $subscription_data = array(
                        "subscription_delete" => '0',
                        "subscription_updatedat" => date('Y-m-d H:i:s')
                    );

                    $rs_subscription = $this->db->update("subscription", $subscription_data, 'subscription_id', $row->subscription_id);
                }
            }
        }

        http_response_code(200);
    }
}
?>
