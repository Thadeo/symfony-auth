<?php
namespace App\Component\Validation;

use App\Component\Exception\ValidationException;

/**
 * Request data validation
 */
class RequestValidation
{
    private $ruleKeys = ['required', 'numeric', 'string', 'amount', 'phone', 'email', 'min', 'max'];

    /**
     * Intialize validate
     * 
     * @param array json post request
     * @param array json rules command
     */
    public function validate(
        array $requestData = [],
        array $data = []
    )
    {
        try {
            // Hold errors
            $errors = [];
            
            // Verify data
            if(empty($data) || empty($requestData)) throw new ValidationException("post request data & data rules command must be in array, not empty");
            
            // Request Data
            foreach ($requestData as $requestKey => $requestValue) {
                
                // Hold Data rules
                $dataRules = (empty($data[$requestKey])) ? null : $data[$requestKey];

                // Verify rules is not empty
                if($dataRules == null) {
                    // Key not exist
                    $errors[$requestKey][] = "data key $requestKey not exist in rules command";
                }else {

                    # verify rules if is string then convert to array
                    $rulesArray = VariableValidation::convertStringToArray('|', $dataRules);

                    // Verify rules if is array
                    if(!is_array($rulesArray)) {
                        # Add errors
                        $errors[$requestKey][] = "Sorry rules value must be in array or string divided with | e.g required|string";
                    
                    }else {
                        
                        # verify variables
                        $validateVariable = $this->validateValue($requestKey, $requestValue, $rulesArray);
                        
                        // Verify if has errors
                        if($validateVariable instanceof ValidationException) $errors[$requestKey] = $validateVariable->getData()['errors'];

                    }
                }

            }//

            // Verify errors if exist
            if(!empty($errors)) throw new ValidationException('Error validation', $errors);

            // Return Response
            return true;

        } catch (ValidationException $th) {
            //throw $th
            return $th->getData();
        }
    }
    
    /**
     * Validate Value
     * 
     * common comand for validation
     * data pass and rules check and validate
     * 
     * @param string requestKey
     * @param string requestValue
     * @param array rules
     */
    protected function validateValue(
        string $requestKey,
        string $requestValue,
        array $rules = []
    )
    {
        try {
            
            // Hold errors
            $errors = [];

            // Validate data
            foreach ($rules as $ruleKey => $ruleValue) {
                
                // Prevent empty ruleKey, because if rule string pass {rule key doesn't included}
                if(empty($ruleKey) || is_numeric($ruleKey)) {
                    $ruleKey = $ruleValue;
                    $ruleValue = '';
                }

                # Verify key
                if(!in_array($ruleKey, $this->ruleKeys)) {

                    // Verify if ruleValue has :
                    if(strpos($ruleKey, ':') !== false) {
                        
                        // Convert colon to array
                        $colData = VariableValidation::convertStringToArray(':', $ruleKey);

                        // Continue to verify
                        $validateRules = $this->validateRules($requestKey, $requestValue, $colData[0], $colData[1]);

                        // Exception
                        if($validateRules instanceof ValidationException) $errors[] = $validateRules->getData()['errors'];

                    }else {
                        $errors[] = "rule $ruleKey not exist, available rules is ".implode('|', $this->ruleKeys);
                    }

                }else {
                    // Continue to verify
                    $validateRules = $this->validateRules($requestValue, $ruleKey, $ruleValue);

                    // Exception
                    if($validateRules instanceof ValidationException) $errors[] = $validateRules->getData()['errors'];
                    
                }
                
            }

            // Verify if errors has value
            if(!empty($errors)) throw new ValidationException("Error validation request", $errors);

            // Return Response
            return true;

        } catch (ValidationException $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Validate Rules
     * 
     * @param string requestValue
     * @param string dataKey
     * @param array rules
     */
    protected function validateRules(
        string $requestValue,
        string $ruleKey,
        string $ruleValue
    )
    {
        try {
            
            // Hold errors
            $errors = null;

            # Check required
            if($ruleKey == 'required' && empty($requestValue)) {
                $errors = "value is required";
            }

            # Check string
            if($ruleKey == 'string' && !VariableValidation::isString($requestValue)) {
                $errors = "value must be a string";
            }

            # Check numeric
            if($ruleKey == 'numeric' && !VariableValidation::isNumeric($requestValue)) {
                $errors = "value must be a numeric";
            }

            # Check amount
            if($ruleKey == 'amount' && !VariableValidation::isAmount($requestValue)) {
                $errors = "value must be a valid amount formated eg. 20 or 20.00";
            }

            # Check email address
            if($ruleKey == 'email' && !VariableValidation::isEmailAddress($requestValue)) {
                $errors = "value must be a valid email address ";
            }

            # Check min, max
            if(in_array($ruleKey, ['min', 'max'])) {

                // Verify value if has value
                if(empty($ruleValue) || empty($requestValue)) {
                    $errors = "$ruleKey value is required";
                }else{
                    // Verify min value
                    if(!VariableValidation::isMin($requestValue, $ruleValue) && $ruleKey == 'min') {
                        $errors = "min value is $ruleValue";
                    }

                    // Verify max value
                    if(!VariableValidation::isMax($requestValue, $ruleValue) && $ruleKey == 'max') {
                        $errors = "max value is $ruleValue";
                    }
                }
            } ##
            

            // Verify if errors has value
            if(!empty($errors)) throw new ValidationException("Error validation request", $errors);

            // Return Response
            return true;

        } catch (ValidationException $th) {
            //throw $th;
            return $th;
        }
    }
}