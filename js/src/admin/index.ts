import app from 'flarum/admin/app';

app.initializers.add('blomstra-migrate-password', () => {

    app.extensionData
        .for('blomstra-migrate-password')
        .registerSetting({
            setting: 'blomstra-migrate-password.importing-password-field',
            label: app.translator.trans('blomstra-migrate-password.admin.setting.importing-password-field'),
            type: 'input',
        })
        .registerSetting({
            setting: 'blomstra-migrate-password.importing-password-field-hash',
            label: app.translator.trans('blomstra-migrate-password.admin.setting.importing-password-field-hash'),
            type: 'select',
            options: {
              md5: 'MD5',
              argon2: 'Argon 2'
            }
        });
});
