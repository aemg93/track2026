import {
  TrendingUp,
  Gift,
  AlertTriangle,
  WalletCards,
  CircleDollarSign,
  Clock,
  User,
  CheckCircle,
  XCircle,
  Play,
  Pause,
  ArrowUpCircle,
  ArrowDownCircle,
} from 'lucide-vue-next'


export default {

  install(app) {


    app.component(
      'IconTrendingUp',
      TrendingUp
    )


    app.component(
      'IconGift',
      Gift
    )


    app.component(
      'IconAlertTriangle',
      AlertTriangle
    )


    app.component(
      'IconWalletCards',
      WalletCards
    )


    app.component(
      'IconCircleDollarSign',
      CircleDollarSign
    )


    app.component(
      'IconArrowUpCircle',
      ArrowUpCircle
    )


    app.component(
      'IconArrowDownCircle',
      ArrowDownCircle
    )



    app.component(
      'IconClock',
      Clock
    )


    app.component(
      'IconUser',
      User
    )


    app.component(
      'IconCheckCircle',
      CheckCircle
    )


    app.component(
      'IconXCircle',
      XCircle
    )


    app.component(
      'IconPlay',
      Play
    )


    app.component(
      'IconPause',
      Pause
    )


  },

}