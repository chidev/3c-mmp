<?php
    include_once "fbmain.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>PHP SDK 3.0 & Graph API base FBConnect Tutorial | Thinkdiff.net</title>
    
        <script type="text/javascript">
            function streamPublish(name, description, hrefTitle, hrefLink, userPrompt){        
                FB.ui({ method : 'feed', 
                        message: userPrompt,
                        link   :  hrefLink,
                        caption:  hrefTitle,
                        picture: 'http://thinkdiff.net/ithinkdiff.png'
               });
               //http://developers.facebook.com/docs/reference/dialogs/feed/
   
            }
            function publishStream(){
                streamPublish("Stream Publish", 'Checkout iOS apps and games from iThinkdiff.net. I found some of them are just awesome!', 'Checkout iThinkdiff.net', 'http://ithinkdiff.net', "Demo Facebook Application Tutorial");
            }
            
            function newInvite(){
                 var receiverUserIds = FB.ui({ 
                        method : 'apprequests',
                        message: 'Come on man checkout my applications. visit http://ithinkdiff.net',
                 },
                 function(receiverUserIds) {
                          console.log("IDS : " + receiverUserIds.request_ids);
                        }
                 );
                 //http://developers.facebook.com/docs/reference/dialogs/requests/
            }
        </script>
    </head>
<body>
    
<style type="text/css">
    .box{
        margin: 5px;
        border: 1px solid #60729b;
        padding: 5px;
        width: 500px;
        height: 200px;
        overflow:auto;
        background-color: #e6ebf8;
    }
</style>

<div id="fb-root"></div>
    <script type="text/javascript" src="http://connect.facebook.net/en_US/all.js"></script>
     <script type="text/javascript">
       FB.init({
         appId  : '<?=$fbconfig['appid']?>',
         status : true, // check login status
         cookie : true, // enable cookies to allow the server to access the session
         xfbml  : true  // parse XFBML
       });
       
     </script>

    <?php if (!$user) { ?>
        You've to login using FB Login Button to see api calling result.
        <a href="<?=$loginUrl?>">Facebook Login</a>
    <?php } else { ?>
        <a href="<?=$logoutUrl?>">Facebook Logout</a>
    <?php } ?>

    <!-- all time check if user session is valid or not -->
    <?php
    if ($user)// && $albums && $photos)
    {
    	foreach ( $albums['data'] as $album )
    	{
    		?>
    		<div class="fb_album" id="fb-album-<?=$album['id']?>">
					<h3><?=$album['name']?></h3>
					<ul>
					<?php foreach( $photos[$album['id']] as $pid => $data ): ?>
						<li><a href="<?=$data['images'][0]['source']?>" rel="fb-album-<?=$album['id']?>" title="<?=$data['name']?>">
							<img src="<?=$data['images'][3]['source']?>" 
									 alt="Pic" 
									 width="<?=floor($data['images'][3]['width']/2)?>" 
									 height="<?=floor($data['images'][3]['height']/2)?>" />
						</a></li>
					<?php


echo '<br/><br/><div style="display:block;background:#f0f0f0;border:2px solid #ccc;margin:20px;padding:15px;color:#333;"><pre>';
var_dump( $data );
echo '</pre></div><hr/><br/><br/><br/><br/>';
die;

					?>
					<? endforeach; ?>
					</ul>
    		</div>
    		<hr/>
    		<?
    	}
		}
		?>
			


    </body>
</html>