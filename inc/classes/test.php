<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *@package Exam Matrix
 * @version 1.0
 * @author Emerico
 */
class Test{

    private $testID;
    private $userTestRegID;
    private $tblMapping;
    private $tblSet;
    private $tblSubset;
    private $tblQuestions;
    private $tblSession;
    public function __construct($testID){
        global $table_prefix;
        if(empty($testID))
            return array('alert'=>'danger','msg'=>'Test Id Not Exist !!');
        $this->testID = $testID;
        $this->tblSet = $table_prefix.'ex_set';
        $this->tblSubset = $table_prefix.'ex_subset';
        $this->tblQuestions = $table_prefix.'ex_questions';
        $this->tblMapping = $table_prefix.'ex_mapping';
        $this->tblSession = $table_prefix.'ex_session';
        $circ = TRUE; 
        while($circ){
            $tempID = 'REG-'.rand(1000, 10000);
            if(!$this->isIdExist($tempID)){
                $this->userTestRegID = $tempID;
                $circ = FALSE;
            }
        }
    }
    public function _getUserRegID(){
        return $this->userTestRegID;
    }
    function isIdExist($id){
        global $wpdb;
        if(empty($id))
            return array('alert'=>'danger','msg'=>'Empty Id Var !!');
        $ck = $wpdb->get_var("SELECT userID FROM $this->tblMapping WHERE regID='$id'");
        return $ck;
    }
    public function _startTest($userRegID,$testId){
        global $wpdb, $current_user;
        $current_user = wp_get_current_user();
        if(empty($userRegID))
            return array('alert'=>'alert-danger','msg'=>'Registration id is invalid !!');
        if(empty($testId))
            return array('alert'=>'alert-danger','msg'=>'Test id is invalid !!');
        if ( !is_user_logged_in() )
            return array('alert'=>'alert-danger','msg'=>'Session Out, Start Again !!');
        if ($this->isIdExist($userRegID))
            return array('alert'=>'alert-danger','msg'=>'Registration id is invalid !!');
        $wpdb->insert( 
                $this->tblMapping, 
                array( 
                        'regID' => $userRegID, 
                        'testID' => $testId, 
                        'userID' => $current_user->ID,
                        'date' => date("Y-m-d H:i:s")
                ), 
                array( 
                        '%s', 
                        '%d',
                        '%d',
                        '%s'
                ) 
        );
        $test_session = $this->_installTestQuestion($userRegID,$testId);
        return array('status' => TRUE, 'session' => $test_session );
    }
    function _installTestQuestion($userRegID,$testID){
        global $wpdb;
        $db = new ExamMatrix\Database();
        if(empty($testID))
            return array('alert'=>'alert-danger','msg'=>'Test Id Empty !!');
        if(empty($userRegID))
            return array('alert'=>'alert-danger','msg'=>'Registration Id Empty !!');
        $questions = $wpdb->get_results( 
                        "
                        SELECT * FROM $this->tblQuestions WHERE `set` = $testID 
                        "
                );
        foreach($questions as $k=>$v){
            $subset_name = $db->getSubsetName($v->subset);
            $test_session[$v->subset][$v->id] = array(
                        'set' => $v->set,
                        'subset' => $v->subset,
                        'question' => $v->question,
                        'opt1' => $v->opt1,
                        'opt2' => $v->opt2,
                        'opt3' => $v->opt3,
                        'opt4' => $v->opt4,
            );
            $wpdb->insert( 
                    $this->tblSession, 
                    array( 
                            'regID' => $userRegID, 
                            'question' => $v->id, 
                            'answer' => 'na',
                    ), 
                    array( 
                            '%s', 
                            '%d',
                            '%s',
                    ) 
            );
        }
        return $test_session;
    }
}
