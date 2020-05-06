<?php
declare(strict_types=1);

namespace Lsia\Atlas\Models\Phinxlog;

use Lsia\Atlas\Models\BaseRecord;

/**
 * @method PhinxlogRow getRow()
 */
class PhinxlogRecord extends BaseRecord
{
    use PhinxlogFields;
}
