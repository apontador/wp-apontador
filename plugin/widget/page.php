<?php echo $before_widget; ?>

<?php echo $before_title; ?>
  <a href="http://apontador.com.br/profile/index/<?php echo $reviews['user']['id']; ?>.html"><?php echo $title; ?></a>
<?php echo $after_title; ?>

<div id="apontador-widget">

  <?php if (!$oauth_token): ?>

  <p><?php _e("please, edit wp-apontador settings in your admin page", "wp-apontador"); ?></p>

  <?php else: ?>
  <ul>
    <?php foreach ( $reviews['user']['reviews'] as $item ): ?>

    <li>
      <a href="<?php echo $item['review']['place']['main_url'] . '#' . $item['review']['id'] ?>">
        <?php echo $item['review']['place']['name']; ?>
      </a>

      <?php if ($showReviewGrade == 2): ?>
        <br />
        <?php
          echo str_repeat($star_tag, (int)$item['review']['rating']);
          echo str_repeat($empty_star_tag, 5 - (int)$item['review']['rating']);
        ?>
      <?php elseif ($showReviewGrade == 1): ?>
        <small>&#8212; <?php echo $this->rating_labels[(int)$item['review']['rating']]; ?></small>
      <?php endif; ?>

      <?php if ($maxChars): ?>
        <p>
          <?php echo $this->limit_str($item['review'], $maxChars, __("more")); ?>
        </p>
      <?php endif; ?>
    </li>

    <?php endforeach; ?>
  </ul>

  <span class="alignright">
    Powred By <a href="http://api.apontador.com.br" target="_blank"><img src="<?php echo plugins_url('/images/icon.gif', dirname(__FILE__)); ?>" alt="Apontador" title="Apontador" /></a>
  </span>

  <?php endif; ?>
</div>

<?php echo $after_widget; ?>
