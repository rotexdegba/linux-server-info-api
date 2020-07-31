<?php
// remember to run `composer require rotexsoft/versatile-collections` once promis-2.0 is migrated to s-edm-pangar
use \VersatileCollections\CollectionInterface;
use \VersatileCollections\ArraysCollection;
use \VersatileCollections\GenericCollection;
use \VersatileCollections\ObjectsCollection;
use function \Slim3MvcTools\Functions\Str\camelToDashes;

// Function to check string starting 
// with given substring 
$functionStrStartsWith = function (string $string, string $startString) {
    $string_len = strlen($string);
    $start_len = strlen($startString);
    return ($string_len >= $start_len) 
           && (substr($string, 0, $start_len) === $startString); 
};

// first traverse the src directory  and include all *.php files and then get declared classes
$src_path  = S3MVC_APP_ROOT_PATH.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR;
$Directory = new RecursiveDirectoryIterator($src_path);
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

// include the php files so that classes contained in them will be declared
foreach ($Regex as $file) {

    //echo $file[0] . PHP_EOL;
    include_once $file[0];
}

$reflection_methods_map = [];
$declared_classes_collection = GenericCollection::makeNew(get_declared_classes(), false);

$action_methods_by_controller_class_name = $declared_classes_collection->filterAll(
    function($key, $current_class_name) {
        return is_subclass_of($current_class_name, \Slim3MvcTools\Controllers\BaseController::class)
               || is_a($current_class_name, \Slim3MvcTools\Controllers\BaseController::class, true);
    }   
) // get a collection of the name of the classes that are instances of \Promis2\Controllers\BaseController
->pipeAndReturnCallbackResult(
    function(CollectionInterface $collection_of_collection_interface_sub_class_names)use($functionStrStartsWith, &$reflection_methods_map) {
    
        $methods_implemented_by_class = [];
        
        $collection_of_collection_interface_sub_class_names->each(
            function($key, $class_name)use(&$methods_implemented_by_class, $functionStrStartsWith, &$reflection_methods_map) {
                $rfclass = new \ReflectionClass($class_name);

                // get an array of \ReflectionMethod objects for the public methods in 
                // \VersatileCollections\CollectionInterface and create a collection of 
                // \ReflectionMethod objects
                $methods_implemented_by_class[$class_name] = 
                    GenericCollection::makeNew(
                        ObjectsCollection::makeNew($rfclass->getMethods(ReflectionMethod::IS_PUBLIC), false)
                        ->filterAll(
                            function($key, \ReflectionMethod $current_method)use($functionStrStartsWith, $rfclass, &$reflection_methods_map, $class_name) {
                            
                                $is_action_method_defined_in_class = 
                                    $current_method->getFileName() === $rfclass->getFileName() // make sure it's not an inherited or trait method
                                    && $functionStrStartsWith($current_method->getName(), 'action');
                                
                                if( $is_action_method_defined_in_class ) {
                                    
                                    $reflection_methods_map[$class_name.':'.$current_method->getName()] = $current_method;
                                }
                                
                                return $is_action_method_defined_in_class;
                            }
                        )
                        ->getName() // calls the getName() method on each
                                    // \ReflectionMethod object in the collection
                                    // via __call magic and returns an array of the names.
                                    // see \VersatileCollections\ObjectsCollection::__call();
                    )
                    ->sort()
                    ->toArray();
            }
        );
        ksort($methods_implemented_by_class);
        
        return ArraysCollection::makeNew($methods_implemented_by_class);
    }
); // Returns a collection whose keys are class names and items are
   // arrays of public method names starting with `action` specific 
   // to the corresponding class

error_reporting(E_ALL);
$objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$report_title = 'Controller Classes by Action Methods Report';
//$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');

$objPHPExcel->getProperties()
            ->setCreator("ProMIS")
            ->setLastModifiedBy("ProMIS Web Developer 2")
            ->setTitle($report_title)
            ->setSubject($report_title)
            ->setDescription("Reports: {$report_title}")
            ->setKeywords("office 2007 openxml php")
            ->setCategory('Reports');

$table_hdr_params_thin_borders = [  
    'fill' => [
         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor'	=> ['argb' => 'CCCCCC']
    ],
    'borders' => [
           'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
         'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
        'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
          'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
    ],
    'font' => [
        'bold' => true
    ]
];
$table_params_thin_borders = $table_hdr_params_thin_borders;
$table_params_thin_borders['font']['bold'] = false;
$table_params_thin_borders['fill']['color']['argb'] = 'FFFFFF';

$table_ftr_param_thick_top_bottom = [  
    'fill' => [
        'type' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor'	=> ['argb' => 'FFFFCC']
    ],
    'borders' => [
        'top' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
      'right' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
     'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK],
       'left' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]
    ],
    'font' => [
        'bold' => true
    ]
];
$curr_row = 1;

$objPHPExcel->setActiveSheetIndex(0);
$active_excel_sheet = $objPHPExcel->getActiveSheet();

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A{$curr_row}", mb_strtoupper($report_title, 'UTF-8'));
$objPHPExcel->getActiveSheet()->getStyle("A{$curr_row}")->getFont()->setBold(true);
$curr_row++;

//write column headers
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("A$curr_row", 'Controller Class Name')
            ->setCellValue("B$curr_row", 'Action Method Name')
            ->setCellValue("C$curr_row", 'Route');

$objPHPExcel->getActiveSheet()
            ->getStyle("A$curr_row")
            ->applyFromArray($table_hdr_params_thin_borders);
$objPHPExcel->getActiveSheet()
            ->getStyle("B$curr_row")
            ->applyFromArray($table_hdr_params_thin_borders);
$objPHPExcel->getActiveSheet()
            ->getStyle("C$curr_row")
            ->applyFromArray($table_hdr_params_thin_borders);
$curr_row++;

if ( $action_methods_by_controller_class_name->count() > 0 ) {
    
    foreach ( $action_methods_by_controller_class_name  as $controller_class_name=>$action_methods ) {
        
        foreach ( $action_methods as $action_method ) {
            
            $ref_meth_obj = $reflection_methods_map["{$controller_class_name}:{$action_method}"];
            $route = camelToDashes($ref_meth_obj->getDeclaringClass()->getShortName())
                . "/" . camelToDashes(str_replace('action', '', $ref_meth_obj->getName()));
            
            foreach ($ref_meth_obj->getParameters() as $parameter) {
                
                if( $parameter->isOptional() ) {
                    
                    $route .= '[/';
                    
                } else {
                    
                    $route .= '/';
                }
                
                $route .= $parameter->getName();
                
                if( $parameter->isDefaultValueAvailable() ) {
                    
                    $route .= '='. var_export($parameter->getDefaultValue(), true);
                }
                
                if( $parameter->isOptional() ) { $route .= ']'; }
            }
            
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue("A$curr_row", $controller_class_name)
                        ->setCellValue("B$curr_row", $action_method)
                        ->setCellValue("C$curr_row", $route);
            
            $active_excel_sheet->getStyle("A{$curr_row}")
                               ->applyFromArray($table_params_thin_borders);
            $active_excel_sheet->getStyle("B{$curr_row}")
                               ->applyFromArray($table_params_thin_borders);
            $active_excel_sheet->getStyle("C{$curr_row}")
                               ->applyFromArray($table_params_thin_borders);
            $curr_row++;
        } // foreach ( $action_methods as $action_method )
    } // foreach ( $action_methods_by_controller_class_name  as $controller_class_name=>$action_methods )
}//if ( $action_methods_by_controller_class_name->count() > 0 )

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="controller-classes-by-action-methods-report.xlsx"');
//header('Cache-Control: max-age=0');
$objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
$objWriter->save('php://output');
exit;
