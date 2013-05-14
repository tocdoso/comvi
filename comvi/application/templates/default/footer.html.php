<div id="footer">
<footer class="footer clearfix">
<p class="pull-left">© <?php echo date('Y');?> Comvi</p>
<?php echo $this->placeholder('footer_links');?>
<p class="pull-right hidden-phone"><?php echo $this->_('Powered by', 'theme');?> <img src="<?php echo $static_url;?>img/comvi24.png" alt="Comvi ! Cloud Framework" width="70" height="24" /></p>
<p class="pull-right visible-desktop"><?php echo $this->_('execution time', 'theme');?>: <?php echo $this->profiler->getExecutionTime();?> - <?php echo $this->_('memory used', 'theme');?>: <?php echo $this->profiler->getMemoryUsed();?> - <?php echo $this->_('files loaded', 'theme');?>: <?php echo Comvi\Core\Loader\FileLoader::countFilesLoaded();?> ·&nbsp;</p>
</footer>
</div>
