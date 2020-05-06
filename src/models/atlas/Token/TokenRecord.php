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
}
