<div class="wrap">
  <h2>Apontador</h2>
  <form method="post" action="<?php menu_page_url("apontador-settings"); ?>">

<?php if ($oauth_secret): ?>

  <p><?php _e("Authenticated as", "wp-apontador") ?></p>
  <div >
	  <img style="margin-right:10px;" align="left" width="64" height="64" src="<?php echo $user['user']['photo_url']; ?> ">
    <p>
      <strong><?php echo $user['user']['name']; ?></strong><br />
      <?php printf(_n("%d review", "%d reviews", $user['user']['stats']['reviews'], "wp-apontador"), $user['user']['stats']['reviews']); ?><br />
      <?php printf(_n("%d photo", "%d photos", $user['user']['stats']['photos'], "wp-apontador"), $user['user']['stats']['photos']); ?><br />
      <?php printf(_n("%d place", "%d places", $user['user']['stats']['places'], "wp-apontador"), $user['user']['stats']['places']); ?><br />
    </p>
  </div>

<?php endif; ?>

  <p>
    <?php _e("click", "wp-apontador"); ?> <a href="<?php echo menu_page_url("apontador-settings", false) . "&request_auth=1"; ?>"><?php _e("here", "wp-apontador"); ?></a>
    <?php _e("to request a new authorization token", "wp-apontador"); ?>
  </p>

  <table class="form-table">
    <tr>
      <th colspan=2>
        <?php _e("Getting your own Consumer Key and Consumer secret for your blog is highly recommended.", "wp-apontador") . '<BR/>'. __("Create a \"new application\" by editting your profile at apontador", "wp-apontador") .',<BR/>' . __("there you can enter your website data and get your own key and secret pair"); ?>
      </th>
    </tr>
    <tr valign="top">
      <th scope="row">
        <strong>Consumer Key</strong>
      </th>
      <td>
        <input type="text" name="consumer_key" value="<?php echo $consumer_key; ?>"/>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <strong>Consumer Secret</strong>
      </th>
      <td>
        <input type="text" name="consumer_secret" value="<?php echo $consumer_secret; ?>"/>
      </td>
    </tr>
    <tr>
      <th colspan=2>
        <?php _e("The following codes are automagically generated, you don't need to worry about them", "wp-apontador") ?> 
      </th>
    </tr>
    <tr valign="top">
      <th scope="row">
        <strong>OAuth Token</strong>
      </th>
      <td>
        <input type="text" name="oauth_token" value="<?php echo $oauth_token ?>" />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <strong>OAuth Secret</strong>
      </th>
      <td>
        <input type="text" name="oauth_secret" value="<?php echo $oauth_secret; ?>"/>
      </td>
    </tr>
  </table>

  <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e("Save Changes"); ?>" />
  </p>
  </form>
</div>
