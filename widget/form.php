<p>
  <label for="<?php echo $this->get_field_name('title'); ?>"><?php _e("Title:"); ?></label>
  <input type="text" id="<?php $this->get_field_id('title'); ?>" name="<?php $this->get_field_name('title'); ?>" value="<?php echo htmlspecialchars($instance['title']); ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_name('howMany'); ?>"><?php _e("How Many:", "wp-apontador"); ?></label>
  <input type="text" id="<?php $this->get_field_id('howMany'); ?>" name="<?php $this->get_field_name('howMany'); ?>" value="<?php echo (int)$instance['howMany']; ?>" />
</p>
<p>
  <label for="<?php echo $this->get_field_name('maxChars'); ?>"><?php _e("Max Length:", "wp-apontador"); ?></label>
  <input type="text" id="<?php $this->get_field_id('maxChars'); ?>" name="<?php $this->get_field_name('maxChars'); ?>" value="<?php echo (int)$instance['maxChars']; ?>" />
</p>
