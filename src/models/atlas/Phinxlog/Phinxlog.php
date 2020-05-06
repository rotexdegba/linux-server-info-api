<?php
declare(strict_types=1);

namespace Lsia\Atlas\Models\Phinxlog;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method PhinxlogTable getTable()
 * @method PhinxlogRelationships getRelationships()
 * @method PhinxlogRecord|null fetchRecord($primaryVal, array $with = [])
 * @method PhinxlogRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method PhinxlogRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method PhinxlogRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method PhinxlogRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method PhinxlogRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method PhinxlogSelect select(array $whereEquals = [])
 * @method PhinxlogRecord newRecord(array $fields = [])
 * @method PhinxlogRecord[] newRecords(array $fieldSets)
 * @method PhinxlogRecordSet newRecordSet(array $records = [])
 * @method PhinxlogRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method PhinxlogRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Phinxlog extends Mapper
{
}
