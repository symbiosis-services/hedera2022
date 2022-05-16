<?php include("template/header.php")?>

<?php 
    include("./config/db.php");

    //Get DB values
    $SQLsentence=$dbconnection->prepare("SELECT sum(hours) Total FROM assets");
    $SQLsentence->execute();
    $totalFund = $SQLsentence->fetch(PDO::FETCH_LAZY) ;
?>

<div class="card-columns">
        <div class="card">
            <img class="card-img-top" src="holder.js/100x180/" alt="">
            <div class="card-body">
                <h1 class="card-title">Grunt Fund</h1>
                <h4 class="card-text">The team has invested <?php echo $totalFund['Total']." hours"; ?></h4>
            </div>
        </div>
</div>

<?php 
    include("./config/db.php");

    //Get DB values
    $SQLsentence=$dbconnection->prepare("SELECT name, contact, details, photo, sum(hours) Shares, (SELECT sum(hours) Total FROM assets) Total, (sum(hours)/(SELECT sum(hours) FROM assets))*100 Slice FROM workers, assets WHERE workers.name = assets.worker group by name");
    $SQLsentence->execute();
    $assetList = $SQLsentence->fetchAll(PDO::FETCH_ASSOC) ;
?>
<?php foreach($assetList as $crrAsset) { ?>
    <div class="col-md-2">
        <div class="card">
            <img class="card-img-top" src="./img/<?php echo $crrAsset['photo']; ?>" alt="">
            <div class="card-body">
                <h4 class="card-title"><?php echo $crrAsset['name']." [".$crrAsset['Shares']." hrs] ".$crrAsset['Slice']." %" ; ?></h4>
                <a href="<?php echo $crrAsset['contact']; ?>">Email</a><br/>
                <a href="<?php echo $crrAsset['details']; ?>">Details</a><br/>
            </div>
        </div>
    </div>
<?php } ?>    

    

<?php include("template/footer.php")?>