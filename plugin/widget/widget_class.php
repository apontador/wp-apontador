<?php
class ApontadorWidget extends WP_Widget
{

  private $rating_labels;

  /**
   * Declares the ApontadorWidget class.
   */
  function ApontadorWidget() {
    $widget_ops = array('classname' => 'apontador_widget', 'description' => __( "Display your reviews from Apontador", "wp-apontador") );
    $control_ops = array('width' => 300, 'height' => 300);
    $this->WP_Widget('apontador', __('Apontador Reviews', "wp-apontador"), $widget_ops, $control_ops);

    $this->rating_labels = array(
      1 => __("terrible", "wp-apontador"),
      2 => __("bad", "wp-apontador"),
      3 => __("regular", "wp-apontador"),
      4 => __("good", "wp-apontador"),
      5 => __("excelent", "wp-apontador")
    );
  }

  /**
   * Displays the Widget
   * @param array $args WordPress' arguments
   * @param array $instance User defined instance arguments
   */
  function widget($args, $instance) {
    extract($args);
    $title = $instance['title'];
    $howMany = $instance['howMany'];
    $maxChars = $instance['maxChars'];
    $showReviewGrade = $instance['showReviewGrade'];
    $oauth_token = get_option('oauth_token');
    $oauth_secret = get_option('oauth_secret');

    # Before the widget
    echo $before_widget;

    # The title
    if ($title) {
      echo $before_title . $title . $after_title;
    }

    $params['limit']=$howMany;
    $params['type']='json';
    $metodo='users/self/reviews';
    $config['key']=get_option('consumer_key');
    $config['secret']=get_option('consumer_secret');

    if (! $oauth_token) {
      _e("please, edit wp-apontador settings in your admin page", "wp-apontador");
    } else {
      $content=apontadorChamaApi($verbo="GET", $metodo, $params, $oauth_token, $oauth_secret,$config);
      $reviews=json_decode($content,true);

      foreach ( $reviews['user']['reviews'] as &$item) {
        $params=array();
        $params['type']='json';
        $metodo='places/' . $item['review']['place']['id'];
        $content=apontadorChamaApi("GET", $metodo, $params,null,null,$config);
        $place = json_decode($content, true);
        $item['review']['place'] = array_merge($item['review']['place'], $place['place']);
      }

      $star_tag = "<img src=\"" . plugins_url('/images/star.png', dirname(__FILE__)) . "\" />";
      $empty_star_tag = "<img src=\"" . plugins_url('/images/mptystar.png', dirname(__FILE__)) . "\" />";

      include dirname(dirname(__FILE__)) . "/widget/page.php";
    }

    # After the widget
    echo $after_widget;
  }

  /**
   * Saves the widgets settings.
   */
  function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['title'] = apply_filters('widget_title', $new_instance['title']);
    $instance['howMany'] = (int)$new_instance['howMany'];
    $instance['maxChars'] = (int)$new_instance['maxChars'];
    $instance['showReviewGrade'] = (int)$new_instance['showReviewGrade'];

    return $instance;
  }

  /**
   * Creates the edit form for the widget.
   * @param array $instance widget instance data
   */
  function form($instance) {
    //Defaults
    $instance = wp_parse_args( 
      (array) $instance,
      array(
        'title'=>'No Apontador',
        'howMany'=>'5',
        'showReviewGrade' => 2,
        'maxChars' => 160
      )
    );

    include dirname(__FILE__) . "/form.php";
  }

  /**
   * A helper function to limit reviews text
   * @param string $str The text to be limited
   * @param integer $length The length of the string to be displayed
   * @param $more_link_text Text of the "more" link
   * @return string
   */
  private function limit_str($review, $length, $more_link_text='') {
    if (strlen($review['content']) > $length) {
      if (!$more_link_text) {
        $more_link_text = __('more');
      }

      return substr($review['content'], 0, $length) . '...'
        . " <a href=\"{$review['place']['main_url']}#{$review['id']}\">{$more_link_text}</a>";
    }

    return $str;
  }
}
