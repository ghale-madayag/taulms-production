<?php
    if (!function_exists('getSelectedTerm')) {
        function getSelectedTerm()
        {
            $term = \App\Models\Term::where('isTerm', '1')->first();
            return $term;
        }
    }

?>