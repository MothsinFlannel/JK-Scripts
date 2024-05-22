import dayjs from 'dayjs'
import utc from 'dayjs/plugin/utc'
import timezone from 'dayjs/plugin/timezone'
import isoWeek from 'dayjs/plugin/isoWeek'

dayjs.extend(utc)
dayjs.extend(timezone)
dayjs.extend(isoWeek)

export default (context, inject) => {
  if (inject) {
    inject('dayjs', dayjs)
  }

  if (context) {
    context.$dayjs = dayjs
  }

  return dayjs
}
