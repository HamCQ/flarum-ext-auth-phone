import { settings } from '@fof-components';
import ExtensionPage from 'flarum/components/ExtensionPage';

const {
    items: { StringItem },
} = settings;

export default class SettingsPage extends ExtensionPage {
    oninit(vnode) {
        super.oninit(vnode);
        this.setting = this.setting.bind(this);
    }
    content() {
        return [
            <div className="SMSSettingsPage">
                <div className="container">
                    <div className="Form-group">
                        <StringItem
                            className="w-300"
                            name={`flarum-ext-auth-phone.sms_ali_access_id`} //数据库存储字段
                            setting={this.setting}
                            {...{ ['required']: true }}
                        >
                            {app.translator.trans(`hamcq-auth-phone.admin.settings.api_sms_ali_access_id`)}
                        </StringItem>
                    </div>
                    <div className="Form-group">
                        <StringItem
                            className="w-300"
                            name={`flarum-ext-auth-phone.sms_ali_access_sec`}
                            setting={this.setting}
                            {...{ ['required']: true }}
                        >
                            {app.translator.trans(`hamcq-auth-phone.admin.settings.api_sms_ali_access_sec`)}
                        </StringItem>
                    </div>
                    <div className="Form-group">
                        <StringItem
                            className="w-300"
                            name={`flarum-ext-auth-phone.sms_ali_sign`}
                            setting={this.setting}
                            {...{ ['required']: true }}
                        >
                            {app.translator.trans(`hamcq-auth-phone.admin.settings.api_sms_ali_sign`)}
                        </StringItem>
                    </div>
                    <div className="Form-group">
                        <StringItem
                            className="w-300"
                            name={`flarum-ext-auth-phone.sms_ali_template_code`}
                            setting={this.setting}
                            {...{ ['required']: true }}
                        >
                            {app.translator.trans(`hamcq-auth-phone.admin.settings.api_sms_ali_template_code`)}
                        </StringItem>
                    </div>
                    <div className="Form-group">
                        <StringItem
                            className="w-300"
                            name={`flarum-ext-auth-phone.api_sms_ali_template_code_traditional`}
                            setting={this.setting}
                            {...{ ['required']: true }}
                        >
                            {app.translator.trans(`hamcq-auth-phone.admin.settings.api_sms_ali_template_code_traditional`)}
                        </StringItem>
                    </div>
                    <div className="Form-group">
                        <StringItem
                            className="w-300"
                            name={`flarum-ext-auth-phone.sms_ali_expire_second`}
                            setting={this.setting}
                            {...{ ['required']: true }}
                        >
                            {app.translator.trans(`hamcq-auth-phone.admin.settings.api_sms_ali_expire_second`)}
                        </StringItem>
                    </div>
                    <hr></hr>
                    {this.buildSettingComponent({
                        type: 'boolean',
                        setting: 'hamcqAuthPhonePostChineseLand',
                        label: app.translator.trans(`hamcq-auth-phone.admin.settings.tips_Chinese_land`),
                    })}
                    <hr></hr>
                    {this.buildSettingComponent({
                        type: 'boolean',
                        setting: 'hamcqAuthPhoneTips',
                        label: app.translator.trans(`hamcq-auth-phone.admin.settings.tips_switch`),
                    })}
                    
                    <div className="tips">
                        {this.buildSettingComponent({
                            className:"phone-tips-content",
                            type: 'string',
                            setting: 'hamcqAuthPhoneTipsOneTitle',
                            placeholder: "#1 "+app.translator.trans(`hamcq-auth-phone.admin.settings.tips_title`),
                        })}
                        {this.buildSettingComponent({
                            className:"phone-tips-content ml-20",
                            type: 'string',
                            setting: 'hamcqAuthPhoneTipsOneUrl',
                            placeholder: app.translator.trans(`hamcq-auth-phone.admin.settings.tips_url`),
                        })}
                        <br/><br/>
                        {this.buildSettingComponent({
                            className:"phone-tips-content",
                            type: 'string',
                            setting: 'hamcqAuthPhoneTipsTwoTitle',
                            placeholder: "#2 "+app.translator.trans(`hamcq-auth-phone.admin.settings.tips_title`),
                        })}
                        {this.buildSettingComponent({
                            className:"phone-tips-content ml-20",
                            type: 'string',
                            setting: 'hamcqAuthPhoneTipsTwoUrl',
                            placeholder: app.translator.trans(`hamcq-auth-phone.admin.settings.tips_url`),
                        })}
                        <br/><br/>
                        {this.buildSettingComponent({
                            className:"phone-tips-content",
                            type: 'string',
                            setting: 'hamcqAuthPhoneTipsThreeTitle',
                            placeholder: "#3 "+app.translator.trans(`hamcq-auth-phone.admin.settings.tips_title`),
                        })}
                        {this.buildSettingComponent({
                            className:"phone-tips-content ml-20",
                            type: 'string',
                            setting: 'hamcqAuthPhoneTipsThreeUrl',
                            placeholder: app.translator.trans(`hamcq-auth-phone.admin.settings.tips_url`),
                        })}
                    </div>

                    <br/><br/><br/>
                    {this.submitButton()}
                </div>
            </div>
        ]
    }
}