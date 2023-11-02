import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';
import Select from 'flarum/common/components/Select';
import Alert from 'flarum/common/components/Alert';
import Link from 'flarum/components/Link';

export default class LinkModal extends Modal {
    oninit(vnode){
        super.oninit(vnode);
        this.region = "ChineseMainland";
    }

    className() {
        return `SMSAuthLinkModal Modal--small`;
    }

    title() {
        return app.translator.trans(`hamcq-auth-phone.forum.modals.link.title`);
    }

    content() {        
        return (
            <div className="Modal-body">
                <div className="Form Form--centered">
                    <div className="Form-group">

                        {Select.component({
                            wrapperAttrs: {
                                className:"f-left"
                            },
                            options: { ChineseMainland: '86', HongKong: '852', Macao:"853", Taiwan:"886" },
                            value: this.region,
                            onchange: (val) => this.region = val,
                        })}

                        <input class="FormControl bottom" 
                            className="phone"
                            style="float:right;width:75%"
                            placeholder={app.translator.trans(`hamcq-auth-phone.forum.modals.link.phone`)}
                            oninput={e => this.phone = e.target.value}
                            disabled={this.inputDisabled}
                        >
                        </input>

                        <input class="FormControl bottom" 
                            className="code" 
                            placeholder={app.translator.trans(`hamcq-auth-phone.forum.modals.link.code`)}
                            oninput={e => this.code = e.target.value}
                            style={{display:this.display ? "block" : "none"}}
                        ></input>

                        <Button className={`Button LogInButton--SMSAuth`} loading={this.loading} disabled={this.loading}
                            onclick={() => this.sendSMS(this.phone,this.region)} style={{display:this.displaySend}}>
                            {app.translator.trans(`hamcq-auth-phone.forum.buttons.send`)}
                        </Button>
                        {this.tips()}
                        <Button className={`Button LogInButton--SMSAuth`} style={{display:this.display ? "block" : "none"}}
                            onclick={() => this.submit(this.phone, this.code, this.region)}>
                            {app.translator.trans(`hamcq-auth-phone.forum.buttons.submit`)}
                        </Button>
                    </div>
                </div>
            </div>
        );
    }

    tips(){
        if(app.forum.data.attributes.hamcqAuthPhoneTips){
            return (
                <div className="phone-tips" style={{display:this.displaySend}}>
                    {app.translator.trans(`hamcq-auth-phone.forum.tips.title`)}
                    <div className="m-t-10">
                        {
                            app.forum.data.attributes.hamcqAuthPhoneTipsOneTitle?
                            <Link style="color:var(--control-color);" href={app.forum.data.attributes.hamcqAuthPhoneTipsOneUrl} 
                            external={true} target="_blank">《{app.forum.data.attributes.hamcqAuthPhoneTipsOneTitle}》
                            </Link>:""
                        }
                        {
                            app.forum.data.attributes.hamcqAuthPhoneTipsTwoTitle?
                            <Link style="color:var(--control-color);" href={app.forum.data.attributes.hamcqAuthPhoneTipsTwoUrl} 
                            external={true} target="_blank">《{app.forum.data.attributes.hamcqAuthPhoneTipsTwoTitle}》
                            </Link>:""
                        }
                        {
                            app.forum.data.attributes.hamcqAuthPhoneTipsThreeTitle?
                            <Link style="color:var(--control-color);" href={app.forum.data.attributes.hamcqAuthPhoneTipsThreeUrl} 
                            external={true} target="_blank">《{app.forum.data.attributes.hamcqAuthPhoneTipsThreeTitle}》
                            </Link>:""
                        }
                    </div>
                </div>
            )
        }
    }

    sendSMS(phone,region) {
        var t = typeof phone;
        if(t != 'string'){
            return;
        }
        this.loading = true;
        this.inputDisabled = true;
        if(phone.length==0){
            this.loading = false;
            this.inputDisabled = false;
            app.alerts.show({ type: 'error' }, 
                app.translator.trans(`hamcq-auth-phone.forum.alerts.wrong_num`)
            );
            return;
        }
        if(region=="ChineseMainland" && phone.length!=11){
            this.loading = false;
            this.inputDisabled = false;
            app.alerts.show({ type: 'error' }, 
                app.translator.trans(`hamcq-auth-phone.forum.alerts.wrong_num`)
            );
            return;
        }
        app
            .request({
                url: app.forum.attribute('apiUrl') + "/auth/sms" + '/send',
                method: 'POST',
                body: { phone, region },
                errorHandler: this.onerror.bind(this),
            }).catch((error) => {
                this.inputDisabled = false;
                app.alerts.show(
                Alert,
                { type: 'error' },
                error
                );
                return;
            }).then((result) => {
                this.loading = false;
                this.display = true;
                
                this.inputDisabled = true;
                this.displaySend = "none";

                if(!result.status){
                    app.alerts.dismiss(alert);
                    switch(result.msg){
                        case "code_exist":
                            app.alerts.show({ type: 'error' }, app.translator.trans(`hamcq-auth-phone.forum.alerts.code_exist`,{
                                time: result.time
                            }));
                            break;
                        case "code_null":
                            app.alerts.show({ type: 'error' }, app.translator.trans(`hamcq-auth-phone.forum.alerts.code_null`));
                            break;
                        case "code_expired":
                            app.alerts.show({ type: 'error' }, app.translator.trans(`hamcq-auth-phone.forum.alerts.code_expired`));
                            break;
                        case "code_invalid":
                            app.alerts.show({ type: 'error' }, app.translator.trans(`hamcq-auth-phone.forum.alerts.code_invalid`));
                            break;
                        case "phone_exist":
                            this.displaySend = "block";
                            this.display = false;
                            this.inputDisabled = false;
                            app.alerts.show({ type: 'error' }, app.translator.trans(`hamcq-auth-phone.forum.alerts.phone_exist`));
                            break;
                        default:
                            this.inputDisabled = false;
                            app.alerts.show({ type: 'error' }, result.msg);
                            break;
                    }
                    return;
                }
                app.alerts.show({ type: 'success' }, app.translator.trans(`hamcq-auth-phone.forum.alerts.send_success`));
            });
    }

    submit(phone,code,region){
        var t = typeof phone;
        var c = typeof code;
        if(t != 'string' || c != 'string'){
            return;
        }
        const user = app.session.user;
        user
          .save({
            phone: phone,
            code: code,
            region:region
          })
          .catch((error) => {
                app.alerts.show(
                Alert,
                { type: 'error' },
                error
                );
                return
            })
          .then((res) => {
                if(res){
                    this.hide();
                    app.alerts.show({ type: 'success' }, app.translator.trans(`hamcq-auth-phone.forum.alerts.link_success`));
                    m.redraw();
                    window.location.reload();
                }
          });
    }
}