<?php
declare(strict_types=1);

namespace Lsia\Atlas\Models\Token;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method TokenTable getTable()
 * @method TokenRelationships getRelationships()
 * @method TokenRecord|null fetchRecord($primaryVal, array $with = [])
 * @method TokenRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method TokenRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method TokenRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method TokenRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method TokenRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method TokenSelect select(array $whereEquals = [])
 * @method TokenRecord newRecord(array $fields = [])
 * @method TokenRecord[] newRecords(array $fieldSets)
 * @method TokenRecordSet newRecordSet(array $records = [])
 * @method TokenRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method TokenRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class Token extends Mapper
{
}
