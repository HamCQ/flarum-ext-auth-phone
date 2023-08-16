import app from 'flarum/app';
import SettingsPage from './components/SettingsPage';
import UserListPage from './components/UserList'

app.initializers.add('hamcq/hamcq-auth-phone', () => {
  app.extensionData.for("hamcq-auth-phone").registerPage(SettingsPage);
  UserListPage();
});
