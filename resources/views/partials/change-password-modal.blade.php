@php
$forcePasswordChange = auth()->check() && is_null(auth()->user()->password_changed_at);
$showPasswordModal = $forcePasswordChange || $errors->updatePassword->isNotEmpty() || session('status') === 'password-updated';
@endphp

<div
    class="modal fade"
    id="changePasswordModal"
    tabindex="-1"
    aria-labelledby="changePasswordModalLabel"
    aria-hidden="true"
    @if($forcePasswordChange) data-bs-backdrop="static" data-bs-keyboard="false" @endif>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="changePasswordModalLabel">
                            {{ $forcePasswordChange ? 'Change Password Required' : 'Change Password' }}
                        </h5>
                        <small class="text-muted">
                            {{ $forcePasswordChange ? 'Please set a new password before continuing.' : 'Update your account password securely.' }}
                        </small>
                    </div>

                    @unless($forcePasswordChange)
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    @endunless
                </div>

                <div class="modal-body">
                    @if(session('status') === 'password-updated')
                    <div class="alert alert-success py-2">
                        Password updated successfully.
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label" for="current_password">Current Password</label>
                        <input
                            class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif"
                            id="current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            required>
                        @if($errors->updatePassword->has('current_password'))
                        <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">New Password</label>
                        <input
                            class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif"
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="new-password"
                            required>
                        @if($errors->updatePassword->has('password'))
                        <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
                        @endif
                    </div>

                    <div>
                        <label class="form-label" for="password_confirmation">Confirm New Password</label>
                        <input
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            required>
                    </div>
                </div>

                <div class="modal-footer">
                    @unless($forcePasswordChange)
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    @endunless
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-key me-1"></i>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const shouldShowPasswordModal = @json($showPasswordModal);
        const modalElement = document.getElementById('changePasswordModal');


        if (shouldShowPasswordModal && modalElement && window.bootstrap) {
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        }
    });
</script>
@endpush