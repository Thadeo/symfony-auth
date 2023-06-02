<?php
namespace App\Component\Validation;

use App\Component\Exception\ValidationException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Data validation
 */
class AppValidation
{
    private $ruleKeys = ['required', 'numeric', 'string', 'amount', 'min', 'max', 'phone', 'email', 'url'];

    public function __construct(
        private TranslatorInterface $lang
    )
    {
        $this->lang = $lang;
    }

    /**
     * Intialize validate
     * 
     * @param array json post request
     * @param array json rules command
     */
    public function validate(
        array $requestData = null,
        array $data = null
    )
    {
        try {
            // Hold errors
            $errors = [];
            
            // Verify data
            if(empty($data) || empty($requestData)) throw new ValidationException($this->lang->trans('validation.data.empty'));
            
            // Request Data
            foreach ($requestData as $requestKey => $requestValue) {
                
                // Hold Data rules
                $dataRules = (empty($data[$requestKey])) ? null : $data[$requestKey];

                // Verify rules is not empty
                if($dataRules == null) {
                    // Key not exist
                    $errors[$requestKey][] = $this->lang->trans('validation.data.key_not_exist', ['%key%' => $requestKey]);
                }else {

                    # verify rules if is string then convert to array
                    $rulesArray = VariableValidation::convertStringToArray('|', $dataRules);

                    // Verify rules if is array
                    if(!is_array($rulesArray)) {
                        # Add errors
                        $errors[$requestKey][] = $this->lang->trans('validation.rules.value_not_string_array');
                    
                    }else {
                        
                        # verify variables
                        $validateVariable = $this->validateValue($requestKey, $requestValue, $rulesArray);
                        
                        // Verify if has errors
                        if($validateVariable instanceof ValidationException) $errors[$requestKey] = $validateVariable->getData()['errors'];

                    }
                }

                // Clean request data
                if(empty($errors)) $requestData[$requestKey] = VariableValidation::cleanInputData($requestValue);

            }//

            // Verify errors if exist
            if(!empty($errors)) throw new ValidationException($this->lang->trans('validation.errors'), $errors);

            // Return Response
            return $requestData;

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
                        $errors[] = $this->lang->trans('validation.rules.key_not_exist', ['%key%' => $ruleKey, '%rules%' => implode('|', $this->ruleKeys)]);
                    }

                }else {
                    // Continue to verify
                    $validateRules = $this->validateRules($requestValue, $ruleKey, $ruleValue);

                    // Exception
                    if($validateRules instanceof ValidationException) $errors[] = $validateRules->getData()['errors'];
                    
                }
                
            }

            // Verify if errors has value
            if(!empty($errors)) throw new ValidationException($this->lang->trans('validation.errors'), $errors);

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
                $errors = $this->lang->trans('validation.rule.value.required');
            }

            # Check string
            if($ruleKey == 'string' && !empty($requestValue) && !VariableValidation::isString($requestValue)) {
                $errors = $this->lang->trans('validation.rule.value.string');
            }

            # Check numeric
            if($ruleKey == 'numeric' && !empty($requestValue) && !VariableValidation::isNumeric($requestValue)) {
                $errors = $this->lang->trans('validation.rule.value.numeric');
            }

            # Check amount
            if($ruleKey == 'amount' && !empty($requestValue) && !VariableValidation::isAmount($requestValue)) {
                $errors = $this->lang->trans('validation.rule.value.amount');
            }

            # Check phone
            if($ruleKey == 'phone' && !empty($requestValue) && !VariableValidation::isPhone($requestValue)) {
                $errors = $this->lang->trans('validation.rule.value.phone');
            }

            # Check email
            if($ruleKey == 'email' && !empty($requestValue) && !VariableValidation::isEmail($requestValue)) {
                $errors = $this->lang->trans('validation.rule.value.email');
            }

            # Check url
            if($ruleKey == 'url' && !empty($requestValue) && !VariableValidation::isUrl($requestValue)) {
                $errors = $this->lang->trans('validation.rule.value.url');
            }

            # Check min, max
            if(in_array($ruleKey, ['min', 'max'])) {

                // Verify value if has value
                if(empty($ruleValue) || empty($requestValue)) {
                    $errors = "$ruleKey ".$this->lang->trans('validation.rule.value.required');
                }else{
                    // Verify min value
                    if(!VariableValidation::isMin($requestValue, $ruleValue) && $ruleKey == 'min') {
                        $errors = $this->lang->trans('validation.rule.value.min', ['%value%' => $ruleValue]);
                    }

                    // Verify max value
                    if(!VariableValidation::isMax($requestValue, $ruleValue) && $ruleKey == 'max') {
                        $errors = $this->lang->trans('validation.rule.value.max', ['%value%' => $ruleValue]);
                    }
                }
            } ##
            

            // Verify if errors has value
            if(!empty($errors)) throw new ValidationException($this->lang->trans('validation.errors'), $errors);

            // Return Response
            return true;

        } catch (ValidationException $th) {
            //throw $th;
            return $th;
        }
    }
}