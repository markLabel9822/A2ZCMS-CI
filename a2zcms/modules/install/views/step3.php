<?php echo validation_errors('<p class="error">', '</p>'); ?>
<?php foreach ($errors as $error): ?>
    <p class="error"><?php echo $error; ?></p>
<?php endforeach; ?>
<?php echo form_open('install/step3',array('class' => 'form-horizontal')); ?>
<p>Please enter your database connection details.</p>
<div class="box form">
    <div class="control-group">
        <label for="hostname" class="control-label">Database Host:<span class="required">*</span></label>
        <div class="controls">
        	<?php echo form_input(array('name' => 'hostname', 'id' => 'hostname', 'value' => set_value('hostname', 'localhost'))); ?>
        </div>
    </div>
    <div class="control-group">
        <label for="username" class="control-label">Username:<span class="required">*</span></label>
        <div class="controls">
        	<?php echo form_input(array('name' => 'username', 'id' => 'username', 'value' => set_value('username','root'))); ?>
        </div>
    </div>
    <div class="control-group">
        <label for="password" class="control-label">Password:</label>
        <div class="controls">
        	<?php echo form_password(array('name' => 'password', 'id' => 'password', 'value' => set_value('password'))); ?>
        </div>
    </div>
    <div class="control-group">
        <label for="database" class="control-label">Database Name:<span class="required">*</span></label>
        <div class="controls">
        	<?php echo form_input(array('name' => 'database', 'id' => 'database', 'value' => set_value('database'))); ?>
        </div>
    </div>
    <div class="control-group">
        <label for="port" class="control-label">Database Port:<span class="required">*</span></label>
        <div class="controls">
        	<?php echo form_input(array('name' => 'port', 'id' => 'port', 'value' => set_value('port', '3306'))); ?>
        </div>
    </div>
    <div class="control-group">
        <label for="prefix" class="control-label">Database Prefix:</label>
        <div class="controls">
        	<?php echo form_input(array('name' => 'prefix', 'id' => 'prefix', 'value' => set_value('prefix','a2z_'))); ?>
        </div>
    </div>
</div>
<div class="control-group">
	<div class="controls">
		<input type="submit" name="submit" class="btn save" value="Next step" />
	</div>
</div>
<?php echo form_close(); ?>