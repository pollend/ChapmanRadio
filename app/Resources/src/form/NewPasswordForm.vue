<template>
    <form>
        <form-group :validator="validator" attribute="oldPassword" name="oldPassword" title="Old Password">
            <input class="form-control" type="password" name="oldPassword" v-model="oldPassword" id="oldPassword">
        </form-group>
        <form-group :validator="validator" attribute="newPassword" name="newPassword" title="New Password">
            <input class="form-control" type="password" name="newPassword" v-model="newPassword" id="newPassword">
        </form-group>
        <form-group :validator="validator" attribute="newPassword_confirm" name="newPassword_confirm" title="Repeat New Password">
            <input class="form-control" type="password" name="newPassword_confirm" v-model="newPassword_confirm" id="newPassword_confirm">
        </form-group>
        <button class="btn btn-default" @click="validateForm" type="button" name="button">Update Password</button>
    </form>
</template>

<script>
  import { Validator } from 'vee-validate'
  import FormGroup from './../components/FormGroup.vue'
  import Alert from './../components/Alert.vue'
  import AccountService from '../service/accountService'
  import Form from './../entity/form'

  export default{
    data () {
      return {
        validator: null,
        oldPassword: '',
        newPassword: '',
        newPassword_confirm: ''
      }
    },
    methods: {
      validateForm: function () {
        const _this = this
        this.validator.validateAll({
          oldPassword: this.oldPassword,
          newPassword: this.newPassword
        }).then(() => {
          AccountService.postChangePassword(this.oldPassword, this.newPassword, (envelope: Form) => {
            if (envelope.code > 0) {
              envelope.fillErrorbag(_this.validator.errorBag)
            } else {
              _this.$emit('success')
            }
          })
        })
      }
    },
    watch: {
      oldPassword (value) {
        this.validator.validate('oldPassword', value)
      },
      newPassword (value) {
        this.validator.validate('newPassword', value)
      },
      newPassword_confirm () {
        this.validator.validate('newPassword', this.newPassword)
      }

    },
    created () {
      this.validator = new Validator()
      this.validator.attach('oldPassword', 'required', { prettyName: 'Old Password' })
      this.validator.attach('newPassword', 'required|confirmed:newPassword_confirm', { prettyName: 'Password' })
    },
    components: {
      FormGroup,
      Alert
    }
  }
</script>