<?php
class ApontadorWidget extends WP_Widget
{
  /**
   * Declares the ApontadorWidget class.
   *
   */
  function ApontadorWidget(){
    $widget_ops = array('classname' => 'apontador_widget', 'description' => __( "Apontador Widget") );
    $control_ops = array('width' => 300, 'height' => 300);
    $this->WP_Widget('apontador', __('Apontador Widget'), $widget_ops, $control_ops);
  }

  /**
   * Displays the Widget
   *
   */
  function widget($args, $instance){
    extract($args);
    $title = apply_filters('widget_title', empty($instance['title']) ? 'No Apontador' : $instance['title']);
    $howMany = empty($instance['howMany']) ? '5' : $instance['howMany'];
    $maxChars = empty($instance['maxChars']) ? '160' : $instance['maxChars'];
    $oauth_token    = get_option('oauth_token');
    $oauth_secret   = get_option('oauth_secret');

    # Before the widget
    echo $before_widget;

    # The title
    if ( $title )
      echo $before_title . $title . $after_title;

    # Make the Hello World Example widget
    echo '<div style="text-align:left;padding:10px;" id="apontador-widget">' ;

    $params['limit']=$howMany;
    $params['type']='json';
    $metodo='users/self/reviews';
    $config['key']=get_option('consumer_key');
    $config['secret']=get_option('consumer_secret');

    if (! $oauth_token) {
      _e('please, edit wp-apontador settings in your admin page');
    } else {
      $content=apontadorChamaApi($verbo="GET", $metodo, $params, $oauth_token, $oauth_secret,$config);
      $reviews=json_decode($content,true);

      foreach ( $reviews['user']['reviews'] as $item) {
        $params=array();
        $params['type']='json';
        $metodo='places/' . $item['review']['place']['id'];
        $content=apontadorChamaApi("GET", $metodo, $params,null,null,$config);
        $place=json_decode($content,true);

        echo '<a href="' . $place['place']['main_url'] . '"><b>' . $place['place']['name'] . '</b></a><br>';
        for ($cunt=0;$cunt<5;$cunt++) {
          if ($cunt< $item['review']['rating']) {
            echo "<img border=0 src=";
            echo plugins_url('/images/star.png', __FILE__);
            echo ">";
      } else {
        echo "<img border=0 src=";
        echo plugins_url('/images/mptystar.png', __FILE__);
        echo ">";
      }
      };
      echo "<br>";
      echo cutstr($item['review']['content'],$maxChars,'...');
      echo '<a href="http://www.apontador.com.br/local/review/' . $item['review']['place']['id'] . '/' . $item['review']['id'] .'.html"> <small>[Veja Mais!]</small></a><br>';
      echo "<br>";
      };
      echo "</div>";
    }

    # After the widget
    echo $after_widget;
  }

  /**
   * Saves the widgets settings.
   *
   */
  function update($new_instance, $old_instance){
    $instance = $old_instance;
    $instance['title'] = strip_tags(stripslashes($new_instance['title']));
    $instance['howMany'] = strip_tags(stripslashes($new_instance['howMany']));
    $instance['maxChars'] = strip_tags(stripslashes($new_instance['maxChars']));

    return $instance;
  }

  /**
   * Creates the edit form for the widget.
   *
   */
  function form($instance){
    //Defaults
    $instance = wp_parse_args( (array) $instance, array('title'=>'', 'howMany'=>'5') );

    $title = htmlspecialchars($instance['title']);
    $howMany = htmlspecialchars($instance['howMany']);
    $maxChars = htmlspecialchars($instance['maxChars']);

    # Output the options
    echo '<p style="text-align:right;"><label for="' . $this->get_field_name('title') . '">' . __('Title:') . ' <input style="width: 250px;" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>';
    # How Many
    echo '<p style="text-align:right;"><label for="' . $this->get_field_name('howMany') . '">' . __('How Many:') . ' <input style="width: 200px;" id="' . $this->get_field_id('howMany') . '" name="' . $this->get_field_name('howMany') . '" type="text" value="' . $howMany . '" /></label></p>';
    echo '<p style="text-align:right;"><label for="' . $this->get_field_name('maxChars') . '">' . __('Max Lenght:') . ' <input style="width: 200px;" id="' . $this->get_field_id('maxChars') . '" name="' . $this->get_field_name('maxChars') . '" type="text" value="' . $maxChars . '" /></label></p>';
  }

}// END class
