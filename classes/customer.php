<?php

class Customer extends Common {

    function __construct() {
        parent::__construct();
    }

    function update() {

        if (!empty($this->post['customer_id']) && !empty($this->post['email'])) {

            $ex = $this->db->query("select * from customer where customer_id='" . $this->post['customer_id'] . "' and customer_delete='0' ");
            if ($row = $this->db->fetch()) {

                $cents = $this->convertToCents($this->post['amount']);

                $plan = \Stripe\Customer::retrieve($row['customer_number']);
                $plan->email = $this->post['email'];
                $plan->save();

                $data = array(
                    "customer_email" => $this->post['email'],
                    "customer_updatedat" => date('Y-m-d H:i:s')
                );

                $result = $this->db->update("customer", $data, "customer_id", $this->post['customer_id']);
                $this->dieSuccess("Customer updated successfully.");
            } else {
                $this->dieError('Something is wrong, please fill all field(s) correctly.');
            }
        } else {
            $this->dieError('Some required filed(s) are missing.');
        }
    }
    
    function retrive() {

        if (!empty($this->post['customer_id'])) {
            $output = array();
            $ex = $this->db->query("select * from customer where customer_id='" . $this->post['customer_id'] . "' and customer_delete='0' ");
            if ($row = $this->db->fetch()) {
                $output['row'] = $row;
                $this->dieSuccess("Customer", $output);
            }
        } else {
            $this->dieError('Customer id is missing.');
        }
    }
}

?>
