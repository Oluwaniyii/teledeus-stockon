<?php 

declare(strict_types=1);

namespace App\Lib ;

class Input 
{
   private $inputGroup ;
   
   public function __construct($inputGroup)
   {
       $this->inputGroup = $inputGroup ;
   }

   public function extract (array $entries): array
   {
       # code...
       $inputGroup = $this->inputGroup;
       $extract = [];

       foreach ($entries as $entry) {
           # if request body has expected entry, return value to array else return empty string
           $extract[$entry] =  isset($inputGroup[$entry]) 
                                        ? $this->filterWhiteSpace($this->clean($inputGroup[$entry])
                                             )
                                        : "" ;
       }
    
       return $extract;
   }

   public function extractPlain ($entries): array
   {
       # code...
       $inputGroup = $this->inputGroup;

       foreach ($entries as $entry) {
          $extract[$entry] =  isset($inputGroup[$entry]) ? $this->filterWhiteSpace($inputGroup[$entry]) : "" ;
       }

   }

   public function extractBuffer() {} //for files

   private function clean(string $input): string {
       # code...
       return htmlspecialchars($input);
   }

   private function filterWhiteSpace (string $input) {
       # code...
       return preg_replace('/\s+/', ' ', $input);
   }

}