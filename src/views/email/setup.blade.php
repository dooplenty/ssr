<!-- Styles -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">

<div class="panel-body">
	<h1>Configuration</h1>

	<form class="form-horizontal" role="form">
		<div class="form-group">
			<label class="col-lg-2 control-label">Protocol</label>
			<div class="col-lg-6">
				<select class="form-control" id="protocol" name="protocol">
					<option value="imap" @if(old('protocol') == 'imap') selected @endif>Imap</option>
					<option value="pop3" @if(old('protocol') == 'pop3') selected @endif>Pop 3</option>	
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-2 control-label">Host Name</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="hostname" name="hostname" value="{{ old('hostname') }}" placeholder=" ">
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-2 control-label">Port</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="port" name="port" value="{{ old('port') }}" placeholder="993">
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-2 control-label">Username</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder=" ">
			</div>
		</div>
		<div class="form-group">
			<label class="col-lg-2 control-label">Password</label>
			<div class="col-lg-6">
				<input type="text" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder=" ">
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-6 col-lg-offset-2">
				<label class="control-label">
					<input type="checkbox" id="ssl" name="ssl" placeholder=" " checked>
					Use SSL?
				</label>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-6 col-lg-offset-2">
				<button type="submit" class="btn btn-primary">Save</button>
			</div>
		</div>
	</form>
</div>