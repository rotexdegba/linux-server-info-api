<?php
declare(strict_types=1);

namespace Lsia\Atlas\Models\TokenUsage;

use Atlas\Mapper\Mapper;
use Atlas\Table\Row;

/**
 * @method TokenUsageTable getTable()
 * @method TokenUsageRelationships getRelationships()
 * @method TokenUsageRecord|null fetchRecord($primaryVal, array $with = [])
 * @method TokenUsageRecord|null fetchRecordBy(array $whereEquals, array $with = [])
 * @method TokenUsageRecord[] fetchRecords(array $primaryVals, array $with = [])
 * @method TokenUsageRecord[] fetchRecordsBy(array $whereEquals, array $with = [])
 * @method TokenUsageRecordSet fetchRecordSet(array $primaryVals, array $with = [])
 * @method TokenUsageRecordSet fetchRecordSetBy(array $whereEquals, array $with = [])
 * @method TokenUsageSelect select(array $whereEquals = [])
 * @method TokenUsageRecord newRecord(array $fields = [])
 * @method TokenUsageRecord[] newRecords(array $fieldSets)
 * @method TokenUsageRecordSet newRecordSet(array $records = [])
 * @method TokenUsageRecord turnRowIntoRecord(Row $row, array $with = [])
 * @method TokenUsageRecord[] turnRowsIntoRecords(array $rows, array $with = [])
 */
class TokenUsage extends Mapper
{
}
