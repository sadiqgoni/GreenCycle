<div class="space-y-4">
    <div>
        <h2 class="text-lg font-bold">Account Information</h2>
    </div>
    <div>
        <dl>
            <dt class="font-semibold">Account Name:</dt>
            <dd>{{ $account->admin_account_name }}</dd>

            <dt class="font-semibold">Account Number:</dt>
            <dd>{{ $account->admin_account_number }}</dd>

            <dt class="font-semibold">Bank Name:</dt>
            <dd>{{ $account->admin_bank_name }}</dd>
        </dl>
    </div>
</div>
