<?php
class EeBot_Filter_MrWilsonTracesKiller implements Zend_Filter_Interface
{
    // tipo do texto, pode ser "name", "title" ou qualquer outra coisa
    private $type = 'name';

    // só se tiver tudo em capslock
    private $capslock = true;

    // número mínimo de termos para executar o filtro
    private $min_terms = 1;

    // número máximo de letras numa palavra para deixá-la toda em minúscula
    private $max_lower = 2;

    // caps ignoráveis
    private $uppercase_list = array(
        'RH', 'ME', 'TI', 'RI', 'BR', 'SA', 'S/A', 'CEO', 'CTO', 'CFO'
    );

    public function lowerCase($texto){
        //Letras minúsculas com acentos
        $texto = strtr($texto, "
        ĄĆĘŁŃÓŚŹŻABCDEFGHIJKLMNOPRSTUWYZQ
        XVЁЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ
        ÂÀÁÄÃÊÈÉËÎÍÌÏÔÕÒÓÖÛÙÚÜÇ
        ", "
        ąćęłńóśźżabcdefghijklmnoprstuwyzq
        xvёйцукенгшщзхъфывапролджэячсмитьбю
        âàáäãêèéëîíìïôõòóöûùúüç
        ");
        return strtolower($texto);
    }

    public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options = func_get_args();
            $temp    = array();
            if (!empty($options)) $temp['type'] = array_shift($options);
            if (!empty($options)) $temp['capslock'] = array_shift($options);
            if (!empty($options)) $temp['min_terms'] = array_shift($options);
            if (!empty($options)) $temp['max_lower'] = array_shift($options);
            if (!empty($options)) $temp['uppercase_list'] = array_shift($options);
            $options = $temp;
        }

        foreach ($options as $id => $option) {
            $this->$id = $options[$id];
        }
    }
 
    public function filter($value)
    {
        $value = trim($value);
        $terms = explode(' ',$value);
        $n_terms = count($terms);
        $new_terms = array();
        $html_decoded = strip_tags(html_entity_decode($value));

        if (isset($value) && $value != null && $value != '' && strlen($value) > 0 && ($this->capslock == true && (strtoupper($value) == $value || strtoupper($html_decoded) == $html_decoded)) || $this->capslock == false) {
            if ($n_terms >= $this->min_terms) {
                // JOAO -> Joao
                // joao -> Joao
                // DA SILVA -> da Silva
                // da silva -> da Silva
                if ($this->type == 'name') {
                    foreach ($terms as $term) {
                        if (strlen($term) > $this->max_lower) {
                            $new_term = $this->lowerCase($term);
                            $new_term[0] = strtoupper($new_term[0]);
                        }
                        else {
                            $new_term = $this->lowerCase($term);
                        }
                        $new_terms[] = $new_term;
                    }
                }
                // WEBDESIGN CORPORATIVO -> Webdesign Corporativo
                // SOLUCOES EM TI -> Solucoes em ti
                // RAD -> RAD (até 4 letras)
                else if ($this->type == 'title') {
                    foreach ($terms as $term) {
                        if (in_array($term, $this->uppercase_list)) {
                            $new_term = $term;
                        }
                        else if (strlen($term) > $this->max_lower) {
                            $new_term = $this->lowerCase($term);
                            $new_term[0] = strtoupper($new_term[0]);
                        }
                        else {
                            $new_term = $this->lowerCase($term);
                        }
                        $new_terms[] = $new_term;
                    }
                }
                // Apenas a primeira letra de cada frase em maiúscula.
                else {
                    foreach ($terms as $term) {
                        $new_terms[] = $this->lowerCase($term);
                    }
                    $new_terms[0][0] = strtoupper($new_terms[0][0]);
                }
                return implode(' ', $new_terms);
            }
            else {
                return $value;
            }
        }
        else {
            return $value;
        }
    }

}