<!-- Author: Ivan Goh Shern Rune -->

@extends('admin.layouts.app')

@section('content')
    <div style="margin-bottom: 20px;">
        <h1>Add New Technician</h1>
        <p style="color: #7F8C8D; font-size: 14px;">Create a new technician account with credentials.</p>
    </div>

    <div x-data="addTechnicianPage()">
        <!-- Success/Error Messages -->
        <div x-show="successMessage" x-cloak
            style="margin-bottom: 15px; padding: 15px; border-radius: 8px; background: #D4EDDA; border: 1px solid #C3E6CB; color: #155724;">
            <strong>✓ Success!</strong>
            <p x-text="successMessage" style="margin: 5px 0 0 0;"></p>
        </div>

        <div x-show="errorMessage" x-cloak
            style="margin-bottom: 15px; padding: 15px; border-radius: 8px; background: #F8D7DA; border: 1px solid #F5C6CB; color: #721C24;">
            <strong>✗ Error!</strong>
            <p x-text="errorMessage" style="margin: 5px 0 0 0;"></p>
        </div>

        <form @submit.prevent="submitForm">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 500; margin-bottom: 5px;">
                        Full Name <span style="color: red;">*</span>
                    </label>
                    <input type="text" x-model="form.name" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #D7DDE5; border-radius: 5px; box-sizing: border-box;"
                        placeholder="Enter full name" />
                    <!-- ✅ 修复这里 -->
                    <template x-if="errors.name">
                        <p x-text="errors.name[0]" style="color: red; font-size: 12px; margin-top: 5px;"></p>
                    </template>
                </div>

                <div>
                    <label style="display: block; font-weight: 500; margin-bottom: 5px;">
                        Email Address <span style="color: red;">*</span>
                    </label>
                    <input type="email" x-model="form.email" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #D7DDE5; border-radius: 5px; box-sizing: border-box;"
                        placeholder="technician@example.com" />
                    <!-- ✅ 修复这里 -->
                    <template x-if="errors.email">
                        <p x-text="errors.email[0]" style="color: red; font-size: 12px; margin-top: 5px;"></p>
                    </template>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 500; margin-bottom: 5px;">
                        Password <span style="color: red;">*</span>
                    </label>
                    <input type="password" x-model="form.password" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #D7DDE5; border-radius: 5px; box-sizing: border-box;"
                        placeholder="Min. 8 characters" />
                    <!-- ✅ 修复这里 -->
                    <template x-if="errors.password">
                        <p x-text="errors.password[0]" style="color: red; font-size: 12px; margin-top: 5px;"></p>
                    </template>
                </div>

                <div>
                    <label style="display: block; font-weight: 500; margin-bottom: 5px;">
                        Confirm Password <span style="color: red;">*</span>
                    </label>
                    <input type="password" x-model="form.password_confirmation" required
                        style="width: 100%; padding: 8px 12px; border: 1px solid #D7DDE5; border-radius: 5px; box-sizing: border-box;"
                        placeholder="Re-enter password" />
                </div>
            </div>

            <div style="display: flex; gap: 10px; padding-top: 20px; border-top: 1px solid #D7DDE5;">
                <button type="submit" :disabled="loading"
                    style="padding: 10px 24px; border-radius: 5px; font-weight: 600; color: white; background: #3498DB; border: none; cursor: pointer;"
                    :style="loading ? 'opacity: 0.6; cursor: not-allowed;' : ''">
                    <span x-show="!loading">Create Technician</span>
                    <span x-show="loading" x-cloak>Creating...</span>
                </button>
                <a href="{{ route('admin.technicians.index') }}"
                    style="padding: 10px 24px; border-radius: 5px; font-weight: 600; color: #2C3E50; background: white; border: 1px solid #D7DDE5; text-decoration: none; display: inline-block;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        function addTechnicianPage() {
            return {
                form: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                },
                errors: {},
                loading: false,
                successMessage: '',
                errorMessage: '',

                async submitForm() {
                    console.log('📤 Form submitting...');
                    
                    this.loading = true;
                    this.errors = {};
                    this.successMessage = '';
                    this.errorMessage = '';

                    try {
                        // ✅ 检查 CSRF token 是否存在
                        const csrfToken = document.querySelector('meta[name="csrf-token"]');
                        if (!csrfToken) {
                            throw new Error('CSRF token not found in page');
                        }

                        console.log('📝 Form data:', this.form);
                        
                        const response = await fetch('/admin/technicians/add', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken.content
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify(this.form)
                        });

                        const json = await response.json();
                        console.log('📥 Response:', json);

                        if (response.ok && json.status === 201) {
                            this.successMessage = json.message || 'Technician account created successfully!';
                            this.form = {
                                name: '',
                                email: '',
                                password: '',
                                password_confirmation: ''
                            };
                            setTimeout(() => {
                                window.location.href = '{{ route('admin.technicians.index') }}';
                            }, 2000);
                        } else if (response.status === 422) {
                            this.errors = json.errors || {};
                            const errorCount = Object.keys(this.errors).length;
                            this.errorMessage = `Please correct ${errorCount} error(s) below.`;
                        } else if (response.status === 403) {
                            this.errorMessage = 'Access denied. Admin privileges required.';
                        } else if (response.status === 401) {
                            this.errorMessage = 'Session expired. Please login again.';
                            setTimeout(() => {
                                window.location.href = '/login';
                            }, 2000);
                        } else {
                            this.errorMessage = json.message || 'An unexpected error occurred.';
                        }
                    } catch (error) {
                        console.error('❌ Error:', error);
                        this.errorMessage = error.message || 'Network error. Please check your connection.';
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
@endsection
