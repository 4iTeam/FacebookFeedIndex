<?php
function get_option($option, $default = false) {
    global $q_notoptions;
    if (!is_array($q_notoptions)) {
        $q_notoptions = array();
    }
    $option = trim($option);
    if (empty($option))
        return false;
    if (isset($q_notoptions[$option])) {
        return $default;
    }
    $alloptions = load_alloptions();

    if (isset($alloptions[$option])) {
        $value = $alloptions[$option];
    } else {
        $o = \App\Model\Option::where('name', $option)->first();
        if ($o) {
            $value = $o->value;
            update_alloptions($option,$value);
        } else {
            $q_notoptions[$option] = true;
            return $default;
        }

    }
    return $value;
}

function add_option($option, $value, $autoload = 'yes') {
    global $q_notoptions;
    if (!is_array($q_notoptions)) {
        $q_notoptions = array();
    }
    $option = trim($option);
    if (empty($option))
        return false;
    // Make sure the option doesn't already exist. We can check the 'notoptions' cache before we ask for a db query
    if (!is_array($q_notoptions) || !isset($q_notoptions[$option]))
        if (false !== get_option($option))
            return false;
    $autoload = ('no' === $autoload || false === $autoload) ? 'no' : 'yes';
    \App\Model\Option::$checkOptionName=false;
    $created = \App\Model\Option::create(['name' => $option, 'value' => $value, 'autoload' => $autoload]);
    \App\Model\Option::$checkOptionName=true;
    if (!$created->id) {
        return false;
    }
    update_alloptions($option,$value);
    // This option exists now
    if (is_array($q_notoptions) && isset($q_notoptions[$option])) {
        unset($q_notoptions[$option]);
    }
    return true;
}

function update_option($option, $value, $autoload = 'yes') {
    $option = trim($option);
    if (empty($option))
        return false;

    $old_value = get_option($option);

    // If the new and old values are the same, no need to update.
    if ($value === $old_value)
        return false;
    if (false === $old_value) {
        return add_option($option, $value, $autoload);
    }
    $autoload = ('no' === $autoload || false === $autoload) ? 'no' : 'yes';
    $o=new \App\Model\Option();
    $o->name=$option;
    $o->setKeyName('name');
    $o->syncOriginal();
    $o->value=$value;
    $o->autoload=$autoload;
    $o->exists=true;
    if($o->save()){
        update_alloptions($option,$value);
        return true;
    }
    return false;
}
function delete_option($option){
    global $q_notoptions;
    if (!is_array($q_notoptions)) {
        $q_notoptions = array();
    }
    \App\Model\Option::where('name',$option)->delete();
    $q_notoptions[$option]=true;//Option deleted and not exits
    update_alloptions($option,null);
}
function load_alloptions() {
    global $q_all_options;
    if (!is_array($q_all_options)) {
        $q_all_options = array();
    }
    if (!$q_all_options) {
        $all = \App\Model\Option::where('autoload', 'yes')->get();
        foreach ($all as $o) {
            $q_all_options[$o->name] = $o->value;
        }
    }
    return $q_all_options;
}

function update_alloptions($option, $value) {
    global $q_all_options;
    if (!is_array($q_all_options)) {
        $q_all_options = array();
    }
    $q_all_options[$option]=$value;
}