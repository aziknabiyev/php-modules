 public function check_letter($txt)
    {
        if(isset($_POST[$txt])) return preg_replace('/[^\p{L}\p{N}.!?[:space:]]/u', '', $_POST[$txt]);
        else return 0; 

    }
    public function check_number($txt)
    {
        return preg_replace('/[^0-9|+\']/', '', $txt);
    }
