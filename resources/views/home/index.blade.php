<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>短链接生成</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-alpha.2/css/materialize.min.css">

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>


<div class="container">
    <div class="header center">
        <div class="col s12">
            <div class="section">
                <h5 class="card-panel z-depth-4">一个给网址生成短链接的网站</h5>
            </div>
            <div class="divider"></div>
        </div>
    </div>

    <div class="row center">
        <form class="col s12" method="post" id="form-data">
            <div class="col s12">
                <div class="input-field ">
                    <i class="material-icons prefix"></i>
                    <input id="icon_prefix" type="text" name="url" class="validate">
                    <label for="icon_prefix">Your URL</label>
                </div>
            </div>
            {{ csrf_field() }}
            <div class="col s12">
                <button class="btn btn-large waves-effect waves-light" id="btn-submit" type="submit" name="action">
                    Submit
                </button>
            </div>
        </form>

    </div>
    <div class="row">
        <div class="card col s12 center hide">
            <div class="card-content">
                <p>
                    <a href="#!" target="_blank"></a>
                </p>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-alpha.2/js/materialize.min.js"></script>
<script>
	$ (function () {
		$ ("#btn-submit").on ('click', function (e) {
			e.preventDefault ();
			var data = $ ("#form-data").serialize ();
            if(!data){
	            var toastHTML = '<span>Please enter your URL</span>';
	            M.toast ({html: toastHTML});
	            return '';
            }
			$.post ('/create', data, function (data) {
				if (data.code === 200) {
					var card = $(".card");
					card.find('a').text(data.data);
					card.find('a').attr('href','//'+data.data);
					card.removeClass('hide');
				} else {

					var toastHTML = '<span>' + data.message + '</span>';
					M.toast ({html: toastHTML});
				}
			},'json');
		});
	});

</script>
</body>
</html>