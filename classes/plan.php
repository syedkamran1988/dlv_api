<?php

class Plan extends Common {

    function __construct() {
        parent::__construct();
    }

    function create() {

        if (!empty($this->post['amount']) &&
                !empty($this->post['interval']) &&
                !empty($this->post['name']) &&
                !empty($this->post['currency'])) {

            $cents = $this->convertToCents($this->post['amount']);
            $id = "Plan_" . rand(111111, 999999);

            $input = array(
                "amount" => $cents,
                "interval" => $this->post['interval'],
                "name" => $this->post['name'],
                "currency" => $this->post['currency'],
                "id" => $id);

            if (intval($this->post['trial_period_days']) > 0) {
                $input['trial_period_days'] = intval($this->post['trial_period_days']);
            }


            $plan = \Stripe\Plan::create($input);
            $plan_id = $plan->id;

            $data = array(
                "plan_number" => $plan_id,
                "plan_name" => $this->post['name'],
                "plan_interval" => $this->post['interval'],
                "plan_currency" => $this->post['currency'],
                "plan_amount" => $this->post['amount'],
                "plan_trial_period_days" => intval($this->post['trial_period_days']),
                "plan_createdat" => date('Y-m-d H:i:s'),
                "plan_updatedat" => date('Y-m-d H:i:s')
            );

            $result = $this->db->insert("plan", $data);
            if ($result) {
                $this->dieSuccess("Plan added successfully.");
            } else {
                $this->dieError('Some went wrong, please fill all field(s) correctly.');
            }
        } else {
            $this->dieError('Some required filed(s) are missing.');
        }
    }

    function update() {

        if (!empty($this->post['plan_id']) && !empty($this->post['name'])) {

            $ex = $this->db->query("select * from plan where plan_id='" . $this->post['plan_id'] . "' and plan_delete='0' ");
            if ($row = $this->db->fetch()) {

                $cents = $this->convertToCents($this->post['amount']);

                $plan = \Stripe\Plan::retrieve($row['plan_number']);
                $plan->name = $this->post['name'];
                $plan->save();

                $data = array(
                    "plan_name" => $this->post['name'],
                    "plan_updatedat" => date('Y-m-d H:i:s')
                );

                $result = $this->db->update("plan", $data, "plan_id", $this->post['plan_id']);
                $this->dieSuccess("Plan updated successfully.");
            } else {
                $this->dieError('Some went wrong, please fill all field(s) correctly.');
            }
        } else {
            $this->dieError('Some required filed(s) are missing.');
        }
    }

    function retrive() {

        if (!empty($this->post['plan_id'])) {
            $output = array();
            $ex = $this->db->query("select * from plan where plan_id='" . $this->post['plan_id'] . "' and plan_delete='0' ");
            if ($row = $this->db->fetch()) {
                $output['row'] = $row;
                $this->dieSuccess("Plan", $output);
            }
        } else {
            $this->dieError('Plan id is missing.');
        }
    }
}

?>
