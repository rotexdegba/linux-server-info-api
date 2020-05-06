<?php
declare(strict_types=1);

namespace Lsia\Atlas\Models\Phinxlog;

use Atlas\Mapper\RecordSet;

/**
 * @method PhinxlogRecord offsetGet($offset)
 * @method PhinxlogRecord appendNew(array $fields = [])
 * @method PhinxlogRecord|null getOneBy(array $whereEquals)
 * @method PhinxlogRecordSet getAllBy(array $whereEquals)
 * @method PhinxlogRecord|null detachOneBy(array $whereEquals)
 * @method PhinxlogRecordSet detachAllBy(array $whereEquals)
 * @method PhinxlogRecordSet detachAll()
 * @method PhinxlogRecordSet detachDeleted()
 */
class PhinxlogRecordSet extends RecordSet
{
}
