<div id="content" class="col-lg-10 col-sm-11 ">
	<div class="row">
		<p>
			<?php
			echo form_open('');
			echo '<div class="box">
			<div class="box-header">
				<h2><i class="icon-th"></i><span class="break"></span>Settings Management</h2>
				<ul id="myTab" class="nav tab-menu nav-tabs">';
			$groups = $content -> not_like('groupname', 'version') -> group_by('groupname') -> get();
			foreach ($groups as $group) {
				echo '<li><a href="#' . $group -> groupname . '">' . ucfirst($group -> groupname) . '</a></li>';
			}
			echo '</ul>
			</div>
			<div class="box-content">
				<div class="tab-content" id="myTabContent">';
			foreach ($groups as $group) {
				echo '<div class="tab-pane active" id="' . $group -> groupname . '">';
				foreach ($content->where('groupname',$group->groupname)->get() as $item) {
					echo '<div class="form-group">
							<div class="col-md-12">';
					echo form_error($item -> varname);
					switch ($item->type) {
						case 'text' :							
							$this -> form_builder -> text($item -> varname, $item -> vartitle, $item -> value, 'form-control');
							break;
						case 'textarea' :
							$this -> form_builder -> textarea($item -> varname, $item -> vartitle, $item -> value, 'form-control');
							break;
						case 'radio' :
							$variables = explode(";", $item -> defaultvalue);
							$radios = array();

							foreach ($variables as $variable) {
								$radios[] = (object) array('id' => $variable, 'name' => $variable);
							}
							$this -> form_builder -> radio($item -> varname, $item -> vartitle, $radios, $item -> value, 'form-control');
							break;
						case 'option' :
							$options = array();
							if (strpos($item -> defaultvalue, ';') === false) {
								foreach (glob(constant($item -> defaultvalue) . '/*', GLOB_ONLYDIR) as $dir) {
									$dir = str_replace(constant($item -> defaultvalue).'/', '', $dir);
									$options[] = (object) array('id' => $dir, 'name' => ucfirst($dir));
								}
							} else {
								$variables = explode(";", $item -> defaultvalue);
								foreach ($variables as $variable) {
									$options[] = (object) array('id' => $variable, 'name' => $variable);
								}
							}
							$this -> form_builder -> option($item -> varname, $item -> vartitle, $options, $item -> value, 'form-control');
							break;
						case 'checkbox' :
							$variables = explode(";", $item -> defaultvalue);
							$checkboxes = array();
							foreach ($variables as $variable) {
								$checkboxes[] = (object) array('id' => $variable, 'name' => $variable);
							}
							$this -> form_builder -> checkboxes($item -> varname, $item -> vartitle, $checkboxes, $item -> value, 'form-control');
							break;
						case 'password' :
							$this -> form_builder -> password($item -> varname, $item -> vartitle, $item -> value, 'form-control');
						case 'date' :
							$this -> form_builder -> date($item -> varname, $item -> vartitle, $item -> value, 'form-control');
					}
					echo "</div>
						</div>";
				}
				echo "</div>";
			}
			echo '</div>
			<div class="form-group">
					<div class="col-md-12">
						<button type="reset" class="btn btn-link close_popup">
							<span class="icon-remove"></span> Cancel
						</button>
						<button type="reset" class="btn btn-default">
							<span class="icon-refresh"></span> Reset
						</button>
						<button type="submit" class="btn btn-success">
							<span class="icon-ok"></span> Update
						</button>
					</div>
				</div>
			</div></div>';
			?>
		</p>
	</div>
</div>
<script>
	$(function() {
		$("#tabs").tabs();
		$('#useravatwidth,#useravatheight,#shortmsg,#pageitem').keyup(function() {
			this.value = this.value.replace(/[^0-9\.]/g, '');
		});
	}); 
</script>