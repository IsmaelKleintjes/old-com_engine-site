<?php defined("_JEXEC") or die("Restricted access"); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <form action="<?php echo JUri::base(); ?>index.php?option=com_engine" method="post" name="adminForm" id="form-validate" class="form-validate form-contact contact-form">
                    <div class="panel-heading"><?php echo JText::_('COM_ENGINE_CONTACT_FORM'); ?></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo $this->form->getLabel('name'); ?>
                                    <?php echo $this->form->getInput('name'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <?php echo $this->form->getLabel('email'); ?>
                                    <?php echo $this->form->getInput('email'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php echo $this->form->getLabel('message'); ?>
                                    <?php echo $this->form->getInput('message'); ?>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-success"><?php echo JText::_('COM_ENGINE_SENT'); ?></button>

                        <div>
                            <input type="hidden" name="task" id="task" value="lead.save" />
                            <?php echo $this->form->getInput('id'); ?>
                            <?php echo $this->form->renderField('captcha'); ?>
                            <?php echo JHtml::_('form.token'); ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>