@csrf
<div class="mb-3"><label>Name</label><input name="name" class="form-control" value="{{ old('name',$customer->name ?? '') }}"></div>
<div class="mb-3"><label>Email</label><input name="email" class="form-control" value="{{ old('email',$customer->email ?? '') }}"></div>
<div class="mb-3"><label>Phone</label><input name="phone" class="form-control" value="{{ old('phone',$customer->phone ?? '') }}"></div>
<div class="mb-3"><label>DOB</label><input type="date" name="dob" class="form-control" value="{{ old('dob',$customer->dob ?? '') }}"></div>
<div class="mb-3"><label>Anniversary</label><input type="date" name="anniversary" class="form-control" value="{{ old('anniversary',$customer->anniversary ?? '') }}"></div>
<div class="mb-3"><label>Notes</label><textarea name="notes" class="form-control">{{ old('notes',$customer->notes ?? '') }}</textarea></div>
<button class="btn btn-success">Save</button>