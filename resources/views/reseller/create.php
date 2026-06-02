<?php include __DIR__ . '/../partials/header.php'; ?>

<main class="dashboard-container">
    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <h2 style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Create New Reseller</h2>

        <form action="<?= url('reseller/store') ?>" method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                
                <!-- Company Name -->
                <div class="form-group" style="grid-column: span 2;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Company Name <span style="color:red;">*</span></label>
                    <input type="text" name="company_name" required placeholder="Enter Company Name" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Company Address -->
                <div class="form-group" style="grid-column: span 2;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Company Address <span style="color:red;">*</span></label>
                    <textarea name="company_address" required rows="2" placeholder="Enter Company Address" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;"></textarea>
                </div>

                <!-- Owner Name -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Owner Name <span style="color:red;">*</span></label>
                    <input type="text" name="owner_name" required placeholder="Enter Owner Name" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Mobile No -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Mobile No. <span style="color:red;">*</span></label>
                    <input type="tel" name="mobile_no" required placeholder="Enter Mobile No." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Web Address -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Web Address</label>
                    <input type="url" name="web_address" placeholder="Enter Web Address" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- E-mail Address -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">E-mail Address <span style="color:red;">*</span></label>
                    <input type="email" name="email" required placeholder="Enter E-mail Address" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Password <span style="color:red;">*</span></label>
                    <input type="password" name="password" required placeholder="Enter Password" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Confirm Password <span style="color:red;">*</span></label>
                    <input type="password" name="confirm_password" required placeholder="Confirm Password" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- District -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">District <span style="color:red;">*</span></label>
                    <select name="district" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <option value="">Select District</option>
                        <option value="Dhaka">Dhaka</option>
                        <option value="Chittagong">Chittagong</option>
                        <option value="Sylhet">Sylhet</option>
                        <option value="Rajshahi">Rajshahi</option>
                        <option value="Khulna">Khulna</option>
                        <option value="Barisal">Barisal</option>
                        <option value="Rangpur">Rangpur</option>
                        <option value="Mymensingh">Mymensingh</option>
                    </select>
                </div>

                <!-- Thana -->
                <div class="form-group">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Thana <span style="color:red;">*</span></label>
                    <input type="text" name="thana" required placeholder="Enter Thana" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

                <!-- Area 1 -->
                <div class="form-group" style="grid-column: span 2;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Area <span style="color:red;">*</span></label>
                    <input type="text" name="area_1" required placeholder="Enter Area" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                </div>

            </div>

            <div style="margin-top: 30px; text-align: right;">
                <a href="<?= url('reseller') ?>" style="padding: 10px 20px; text-decoration: none; color: #475569; margin-right: 15px;">Cancel</a>
                <button type="submit" style="padding: 10px 25px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Create Reseller</button>
            </div>
        </form>
    </div>
</main>

<script>
    // Basic JS to check password matching before submit
    document.querySelector('form').addEventListener('submit', function(e) {
        let pass = document.querySelector('input[name="password"]').value;
        let confirm = document.querySelector('input[name="confirm_password"]').value;
        
        if (pass !== confirm) {
            e.preventDefault();
            alert('Passwords do not match!');
        }
    });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
