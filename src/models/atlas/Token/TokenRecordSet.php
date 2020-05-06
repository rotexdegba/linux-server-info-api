<?php
declare(strict_types=1);

namespace Lsia\Atlas\Models\Token;

use Atlas\Mapper\RecordSet;

/**
 * @method TokenRecord offsetGet($offset)
 * @method TokenRecord appendNew(array $fields = [])
 * @method TokenRecord|null getOneBy(array $whereEquals)
 * @method TokenRecordSet getAllBy(array $whereEquals)
 * @method TokenRecord|null detachOneBy(array $whereEquals)
 * @method TokenRecordSet detachAllBy(array $whereEquals)
 * @method TokenRecordSet detachAll()
 * @method TokenRecordSet detachDeleted()
 */
class TokenRecordSet extends RecordSet
{
}
