@csrf
<div class="mb-3">
    <label>Title</label>
    <input type="text" name="title" class="form-control" value="{{ old('title',$task->title ?? '') }}" required>
</div>
<div class="mb-3">
    <label>Description</label>
    <textarea name="description" class="form-control">{{ old('description',$task->description ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label>Assign To</label>
    <select name="assigned_to" class="form-control">
        @foreach($users as $user)
            <option value="{{ $user->id }}" @selected(old('assigned_to',$task->assigned_to ?? '')==$user->id)>{{ $user->name }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-control" required>
        <option value="pending" {{ (old('status', $task->status ?? 'pending') == 'pending') ? 'selected' : '' }}>Pending</option>
        <option value="in_progress" {{ (old('status', $task->status ?? '') == 'in_progress') ? 'selected' : '' }}>In Progress</option>
        <option value="completed" {{ (old('status', $task->status ?? '') == 'completed') ? 'selected' : '' }}>Completed</option>
    </select>
    @error('status')<div class="text-danger">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label>Due Date</label>
    <input type="date" name="due_date" class="form-control" value="{{ old('due_date',$task->due_date ?? '') }}">
</div>
<button class="btn btn-success">Save</button>