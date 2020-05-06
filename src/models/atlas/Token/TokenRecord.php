<?php
declare(strict_types=1);

namespace Lsia\Atlas\Models\Token;

use Lsia\Atlas\Models\BaseRecord;

/**
 * @method TokenRow getRow()
 */
class TokenRecord extends BaseRecord
{
    use TokenFields;
    
    public function __construct(\Atlas\Table\Row $row, \Atlas\Mapper\Related $related) {
        
        parent::__construct($row, $related);
        
        // see https://www.sirius.ro/php/sirius/validation/simple_example.html
        // see https://www.sirius.ro/php/sirius/validation/validation_rules.html
        $this->siriusValidationRules = [
            //'generators_username',
            //'token',
            //'date_created',
            //'date_last_edited',
            [
                'max_requests_per_day:Maximum Api Requests per Day', 
                [
                    'required', 
                    ['integer', '', '{label} must be an integer']
                ]
            ], 
            [
                'expiry_date:Expiry Date', 
                [
                    'required', 
                    ['datetime', '', '{label} must be a date having the format YYYY-MM-DD HH:mm:ss']
                ]
            ],
            //'creators_ip'
        ];
    }
}
