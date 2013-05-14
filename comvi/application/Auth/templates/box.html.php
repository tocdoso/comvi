<?php $return_url = $this->build($this->current_url);?>
<?php if ($user->email):?>
<p class="user-dropdown pull-right"><img class="avatar" src="<?php echo $user->avatar;?>" alt="<?php echo $user->fullname;?>" width="32" height="32" /> <?php echo $user->email;?> · <a href="<?php echo $this->build($this->url('?controller=Auth/Controller&task=Logout&return='.$return_url));?>"><?php echo $this->_('Logout');?></a></p>
<?php else:?>
<p class="user-dropdown pull-right"><a href="<?php echo $this->build($this->url('?controller=Auth/Controller&task=Login&identifier=google&return='.$return_url));?>"><?php echo $this->_('Login');?></a></p>
<?php endif;?>
