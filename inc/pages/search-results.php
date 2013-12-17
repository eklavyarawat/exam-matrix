<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
if(isset($_REQUEST['submitSearch'])){
    if(empty($_REQUEST['username']) && empty($_REQUEST['regid'])){
        $alert = array('alert'=> 'alert-danger', 'msg'=> 'Please fill one of the criteria !!');
    } elseif (!empty($_REQUEST['username'])) {
        $username = trim($_REQUEST['username']);
        $alert = $db->getAllTestByUsername($username);
        if(!isset($alert['msg']))
            $tests = $alert;
    } elseif (!empty($_REQUEST['regid'])) {
        $regid = trim($_REQUEST['regid']);
        $alert = $db->getAllTestByRegID($regid);
        if(!isset($alert['msg']))
            $tests = $alert;
    }
    
}
if(isset($_REQUEST['viewResult'])){
    $regID = trim($_REQUEST['viewResult']);
}
?>
<div class="qWrap">
<br/><p><h1> Search Result </h1></p><hr/><br/><br/>
<?php if($alert['msg'] != ''){ ?>
<div class="alert <?php echo $alert['alert']; ?>"><?php echo $alert['msg']; ?></div>
<?php } ?>
<!-- Filter -->
<div class="ex-question">
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title">Search Filter</h3>
      </div>
      <div class="panel-body">
          <form method="post" action="">
              <ul class="filter">
                  <li>
                    <label for="username">Username : </label>
                    <input type="text" name="username" value="" />
                  </li>
                  <li>
                    <label for="regid">Reg / Test ID : </label>
                    <input type="text" name="regid" value="" />
                  </li>
                  <li>
                    <input type="submit" class="btn btn-sm btn-primary" name="submitSearch" value="Search" />
                  </li>
              </ul>
          </form>
      </div>
    </div>
</div>
<!-- Recent Questions -->    
<?php if($tests){ ?>
<div class="questions">
    <div class="list-group">
        <ul>
            <li class="list-group-item active" style="font-size:16px;">Last 20 Results</li>
                <li class="list-group-item item-set">
                    <ul>
                        <li style="width: 30px;"><strong>ID</strong></li>
                        <li style="width: 240px;"><strong>Exam Name</strong></li>
                        <li style="width: 60px;"><strong>REG ID</strong></li>
                        <li style="width: 60px;"><strong>Total</strong></li>
                        <li style="width: 60px;"><strong>Gain</strong></li>
                        <li style="width: 60px;"><strong>Wrong</strong></li>
                    </ul>
                </li>
            <?php foreach($tests as $key => $value){ ?>
                <li class="list-group-item item-set">
                    <ul>
                        <li style="width: 30px;"> <?php echo $value->ID; ?> </li>
                        <li style="width: 240px;"><?php echo substr($db->getExamName($value->regID),0,40); ?></li>
                        <li style="width: 60px;"><?php echo $value->regID; ?></li>
                        <li style="width: 60px;"><?php echo $value->total; ?></li>
                        <li style="width: 60px;"><?php echo $value->gain; ?></li>
                        <li style="width: 60px;"><?php echo $value->wrong; ?></li>
                    </ul>
                        <?php echo $value->name; ?>
                    <form class="viewResult" name="viewResult-<?php echo $value->ID; ?>" action="" method="post">
                        <input type="hidden" name="viewResult" value="<?php echo $value->regID; ?>" />
                        <a class="close">></a>
                    </form>
                </li>
             <?php } ?> 
        </ul>
    </div>
</div>
<?php } ?>
</div>