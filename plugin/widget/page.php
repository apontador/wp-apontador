<div id="apontador-widget">

  <?php if (!$oauth_token): ?>

  <p><?php _e("please, edit wp-apontador settings in your admin page", "wp-apontador"); ?></p>

  <?php else: foreach ( $reviews['user']['reviews'] as $item ): ?>

  <a href="<?php echo $item['review']['place']['main_url']; ?>">
    <strong><?php echo $item['review']['place']['name']; ?></strong>
  </a>
  <br />

  <?php if ($showReviewGrade == 2): ?>
    <?php echo str_repeat($star_tag, (int)$item['review']['rating']); ?>
    <?php echo str_repeat($empty_star_tag, 5 - (int)$item['review']['rating']); ?>
  <?php elseif ($showReviewGrade == 1): ?>
    <small><?php echo $this->rating_labels[(int)$item['review']['rating']]; ?></small>
  <?php endif; ?>

  <p>
    <?php echo $this->limit_str($item['review']['content'], $maxChars, '...'); ?>
    <a href="http://www.apontador.com.br/local/review/<?php echo $item['review']['place']['id'] . "/" . $item['review']['id']; ?>.html">mais</a>
  </p>

  <?php endforeach; ?>

  <span class="alignright">
    Powred By <a href="http://apontador.com.br" target="_blank"><img src="<?php echo plugins_url('/images/icon.gif', dirname(__FILE__)); ?>" alt="Apontador" title="Apontador" /></a>
  </span>

  <?php endif; ?>
</div>
