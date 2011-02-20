<p>
  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title:"); ?></label>
  <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo htmlspecialchars($instance['title']); ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_id('howMany'); ?>"><?php _e("How Many:", "wp-apontador"); ?></label>
  <input type="text" id="<?php echo $this->get_field_id('howMany'); ?>" name="<?php echo $this->get_field_name('howMany'); ?>" value="<?php echo (int)$instance['howMany']; ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_id('maxChars'); ?>"><?php _e("Max Length:", "wp-apontador"); ?></label>
  <input type="text" id="<?php echo $this->get_field_id('maxChars'); ?>" name="<?php echo $this->get_field_name('maxChars'); ?>" value="<?php echo (int)$instance['maxChars']; ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_id('showReviewGrade'); ?>"><?php _e("Show Review Grade:", "wp-apontador"); ?></label>
  <select name="<?php echo $this->get_field_name('showReviewGrade'); ?>" id="<?php echo $this->get_field_id('showReviewGrade'); ?>">
    <option value="2"<?php echo (int)$instance['maxChars'] == 2 ? " selected=\"selected\"" : ""; ?>><?php _e("as stars", "wp-apontador"); ?></option>
    <option value="1"<?php echo (int)$instance['maxChars'] == 1 ? " selected=\"selected\"" : ""; ?>><?php _e("as text", "wp-apontador"); ?></option>
    <option value="0"<?php echo (int)$instance['maxChars'] == 0 ? " selected=\"selected\"" : ""; ?>><?php _e("hide", "wp-apontador"); ?></option>
  </select>
</p>
