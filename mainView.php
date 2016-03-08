<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cut URL</title>
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
</head>
<body>

<form action="http://minurl.lo" method="post" role="form" class="form-horizontal col-md-7">

    <div class="form-group row">
        <label for="userLink" class="control-label col-md-3">Past link</label>
        <div class="col-md-9">
            <input type="text" name="link" class="form-control" id="userLink" placeholder="Paste link here">
        </div>
    </div>

    <div class="form-group">
        <label for="toTime" class="control-label col-md-3">Enter max time for link</label>
        <div class="col-md-9">
            <input type="text" name="time" class="form-control" id="toTime" placeholder="Enter time">
            <p class="help-block">Time like 30 = 30 sec. 1:30 = 1 hour, 30 sec.</p>
        </div>

        <label for="userShLink" class="control-label col-md-3">Enter your short link</label>
        <div class="col-md-9">
            <input type="text" name="userShLink" class="form-control" id="shortLink"
                   placeholder="Enter your shorts for link">
            <p class="help-block">Link like AbCd convert to minurl.lo/AbCd</p>
        </div>

        <div class="col-md-offset-3 col-md-9">
            <button type="submit" class="btn btn-success">Get short link</button>
        </div>

    </div>
</form>
<?php if ($shortLink): ?>
    <div lass="col-md-5">
        <div class="alert-info col-md-4">
            <p class="text-info">Copy your short link: </p>

        </div>
        <a href="<?= $link ?>" ><?= $shortLink ?></a>
    </div>
<?php endif ?>

</body>
</html>




