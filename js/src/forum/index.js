import { extend } from 'flarum/extend';
import addAlert from './addAlert';
import app from 'flarum/app';

export * from './components';
import SettingsPage from 'flarum/components/SettingsPage';
// import SMSApplication from './components/SMS';

import UnlinkModal from "./components/UnlinkModal";
import LinkModal from "./components/LinkModal";

import Button from 'flarum/components/Button';

//packname:flarum-ext-auth-phone  modulename:hamcq-auth-phone
app.initializers.add('hamcq/flarum-ext-auth-phone', () => {
    addAlert();

    extend(SettingsPage.prototype, 'accountItems', (items) => {
      const {
          data: {
              attributes: {
                  SMSAuth: {
                      isAuth = false
                  },
              },
          },
      } = app.session.user;

      items.add(`linkSMSAuth`,
          <Button className={`Button linkSMSAuthButton--${isAuth ? 'danger' : 'success'}`} icon="fas fa-id-badge"
              onclick={() => app.modal.show(isAuth ? UnlinkModal : LinkModal)}>
              {app.translator.trans(`hamcq-auth-phone.forum.buttons.${isAuth ? 'unlink' : 'link'}`)}
          </Button>
      );
  });
});

// app.qq = new SMSApplication();