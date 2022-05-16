<?php
    include("./template/header.php");

    $txtId=(isset($_POST['txtId']))?$_POST['txtId']:"";
    $txtWorker=(isset($_POST['txtWorker']))?$_POST['txtWorker']:"";
    $txtTask=(isset($_POST['txtTask']))?$_POST['txtTask']:"";
    $txtHours=(isset($_POST['txtHours']))?$_POST['txtHours']:"";
    $txtProject=(isset($_POST['txtProject']))?$_POST['txtProject']:"";
    $txtTask_Type=(isset($_POST['txtTask_Type']))?$_POST['txtTask_Type']:"";
    $txtDeliverable=(isset($_POST['txtDeliverable']))?$_POST['txtDeliverable']:"";

    $txtFile=(isset($_FILES['txtFile']['name']))?$_FILES['txtFile']['name']:"";
    $button_action=(isset($_POST['button_action']))?$_POST['button_action']:"";

    include("./config/db.php");

    try {
        switch($button_action){
            case "Add":
                //$SQLsentence=$dbconnection->prepare("INSERT INTO `assets` (`id`, `worker`, `task`, `hours`, `project`, `task_type`, `deliverable`) VALUES (NULL, $txtWorker,  $txtTask, $txtHours, $txtProject, $txtTask_Type,  $txtDeliverable)");
                //$SQLsentence=$dbconnection->prepare("INSERT INTO `assets` (`id`, `worker`, `task`, `hours`, `project`, `task_type`, `deliverable`) VALUES (NULL, 'Saul', 'Testing demo', '1', 'Symbiosis', 'time', 'Symbiosis demo')");
                $SQLsentence=$dbconnection->prepare("INSERT INTO assets (worker, task, hours, project, task_type, deliverable, evidence) VALUES (:worker, :task, :task_time, :project, :task_type, :deliverable, :evidence)");
                $SQLsentence->bindParam(':worker',$txtWorker);
                $SQLsentence->bindParam(':task',$txtTask);
                $SQLsentence->bindParam(':task_time',$txtHours);
                $SQLsentence->bindParam(':project',$txtProject);
                $SQLsentence->bindParam(':task_type',$txtTask_Type);
                $SQLsentence->bindParam(':deliverable',$txtDeliverable);

                $fileName=($txtFile!=""?$txtWorker."_".$txtId."_".$_FILES['txtFile']['name']:"no_evidence");
                $tmpFile= $_FILES['txtFile']['tmp_name'];
                if($tmpFile!=""){
                    move_uploaded_file($tmpFile,"./evidence/".$fileName);
                }

                $SQLsentence->bindParam(':evidence',$fileName);
                
                $SQLsentence->execute();
                
                header("Location:registry.php");
                break;
            
            case "Delete":
                //Delete fle
                $SQLsentence=$dbconnection->prepare("select evidence from assets where id =:id ");
                $SQLsentence->bindParam(':id',$txtId);
                $SQLsentence->execute();
                $evidenceSelected = $SQLsentence->fetch(PDO::FETCH_LAZY) ;

                if(isset($evidenceSelected["evidence"]) && ($evidenceSelected["evidence"] != "evidence.jpg") ){
                    if(file_exists("./evidence/".$evidenceSelected["evidence"])){
                        unlink("./evidence/".$evidenceSelected["evidence"]);
                    }
                }

                //echo "Delete registry";
                $SQLsentence=$dbconnection->prepare("delete from assets where id =:id ");
                $SQLsentence->bindParam(':id',$txtId);
                $SQLsentence->execute();
                
                header("Location:registry.php");
                break;
            case "Select":
                //echo "Select registry";
                $SQLsentence=$dbconnection->prepare("select * from assets where id =:id ");
                $SQLsentence->bindParam(':id',$txtId);
                $SQLsentence->execute();
                $assetSelected = $SQLsentence->fetch(PDO::FETCH_LAZY) ;

                $txtWorker = $assetSelected['worker'];
                $txtTask = $assetSelected['task'];
                $txtHours = $assetSelected['hours'];
                $txtProject = $assetSelected['project'];
                $txtTask_Type = $assetSelected['task_type'];
                $txtDeliverable = $assetSelected['deliverable'];
                $txtFile = $assetSelected['evidence'];

                break;
            case "Update":    
                //If user include a new file, Insert new file and delete previous
                $fileName=($txtFile!=""?$txtWorker."_".$txtId."_".$_FILES['txtFile']['name']:"no_evidence");

                if($txtFile!=""){
                    //Insert file
                    $tmpFile= $_FILES['txtFile']['tmp_name'];
                    if($tmpFile!=""){
                        move_uploaded_file($tmpFile,"./evidence/".$fileName);
                    }
                    //Delete file
                    $SQLsentence=$dbconnection->prepare("select evidence from assets where id =:id ");
                    $SQLsentence->bindParam(':id',$txtId);
                    $SQLsentence->execute();
                    $evidenceSelected = $SQLsentence->fetch(PDO::FETCH_LAZY) ;
    
                    if(isset($evidenceSelected["evidence"]) && ($evidenceSelected["evidence"] != "no_evidence") ){
                        if(file_exists("./evidence/".$evidenceSelected["evidence"])){
                            unlink("./evidence/".$evidenceSelected["evidence"]);
                        }
                    }
    
                }
                //echo "Update registry";
                $SQLsentence=$dbconnection->prepare("update assets set worker= :worker, task=:task, hours=:task_time, project=:project, task_type=:task_type, deliverable=:deliverable, evidence=:evidence where id=:id");
                $SQLsentence->bindParam(':id',$txtId);
                $SQLsentence->bindParam(':worker',$txtWorker);
                $SQLsentence->bindParam(':task',$txtTask);
                $SQLsentence->bindParam(':task_time',$txtHours);
                $SQLsentence->bindParam(':project',$txtProject);
                $SQLsentence->bindParam(':task_type',$txtTask_Type);
                $SQLsentence->bindParam(':deliverable',$txtDeliverable);
                $SQLsentence->bindParam(':evidence',$fileName);
                
                $SQLsentence->execute();              

                break;
            case "Refresh":
                //echo "Cancel registry";
                header("Location:registry.php");
                break;
        }   
        //Get DB values
        $SQLsentence=$dbconnection->prepare("select * from assets");
        $SQLsentence->execute();
        $assetList = $SQLsentence->fetchAll(PDO::FETCH_ASSOC) ;
        //echo "Database updated..."; 
    }catch (Exception $ex) {
        echo $ex->getMessage();
    }

?>
    
        <div class="card">
            <div class="card-header">
                <h2>Register assets</h2>       
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'];?>">

                <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Worker</th>
                <th>Task</th>
                <th>Hours</th>
                <th>Project</th>
                <th>Asset</th>
                <th>Deliverable</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input required readonly type="text" class="form-control" value="<?php echo $txtId; ?>" name="txtId" id="txtId" placeholder="Id"></td>
                <td><input type="text" required class="form-control" value="<?php echo $txtWorker; ?>" name="txtWorker" id="txtWorker" placeholder="Worker"></td>
                <td><input type="text" required class="form-control" value="<?php echo $txtTask; ?>" name="txtTask" id="txtTask" placeholder="Task"> </td>
                <td><input type="integer" required class="form-control" value="<?php echo ($txtHours!=""?$txtHours:0); ?>" name="txtHours" id="txtHours" placeholder=0></td>
                <td><input type="text" required class="form-control" value="<?php echo ($txtProject!=""?$txtProject:"Symbiosis"); ?>" name="txtProject" id="txtProject" placeholder="Project"></td>
                <td><input type="text" required class="form-control" value="<?php echo ($txtTask_Type!=""?$txtTask_Type:"time"); ?>" name="txtTask_Type" id="txtTask_Type" placeholder="Asset Type"></td>
                <td><input type="text"  class="form-control" value="<?php echo $txtDeliverable; ?>" name="txtDeliverable" id="txtDeliverable" placeholder="Deliverable"></td>
            </tr>
        </tbody>
    </table>


                    <div class = "form-group">
                        <label for="txtFile">Evidence: <?php echo $txtFile; ?></label>
                        <input type="file" class="form-control"  name="txtFile" id="txtFile" placeholder="File">
                        <small id="fileHelp" class="form-text text-muted">Please select a file to upload.</small>
                    </div>

                    <div class="btn-group" role="group" aria-label="">
                            <button type="submit" name="button_action" <?php echo ($button_action=="Select"?"disabled":"") ?> value="Add" class="btn btn-success">Add</button>
                            <button type="submit" name="button_action" <?php echo ($button_action!="Select"?"disabled":"") ?> value="Update" class="btn btn-warning">Update</button>
                            <button type="submit" name="button_action" <?php echo ($button_action!="Select"?"disabled":"") ?> value="Refresh" class="btn btn-info">Refresh</button>
                    </div>           
                </form>                
            </div>
        </div>
   
  
    <div class="col-md-12">
        <h2>Asset Table </h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Worker</th>
                    <th>Task</th>
                    <th>Hours</th>
                    <th>Project</th>
                    <th>Asset</th>
                    <th>Deliverable</th>
                    <th>Evidence</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($assetList as $crrAsset) { ?>
                <tr>
                    <td><?php echo $crrAsset['id']; ?></td>
                    <td><?php echo $crrAsset['worker']; ?></td>
                    <td><?php echo $crrAsset['task']; ?></td>
                    <td><?php echo $crrAsset['hours']; ?></td>
                    <td><?php echo $crrAsset['project']; ?></td>
                    <td><?php echo $crrAsset['task_type']; ?></td>
                    <td><?php echo $crrAsset['deliverable']; ?></td>
                    <td><?php echo $crrAsset['evidence']; ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="txtId" id="txtId" value=<?php echo $crrAsset['id']; ?>>
                            <input type="submit" name="button_action" value="Select" class="btn btn-primary" />
                            <input type="submit" name="button_action" value="Delete" class="btn btn-danger" />
                        </form>
                    </td>
                </tr>

            <?php }  ?>

            </tbody>
        </table>
    </div>

<?php
 
?>
<?php include("./template/footer.php"); ?>
