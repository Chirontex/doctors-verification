const DocsVer = {
    countdown: () => {
        const timer = document.getElementById('docsver-timer')

        let time = timer.innerHTML.split(':')

        if (time[1] == '00')
        {
            if (time[0] != '00')
            {
                time[1] = 59

                time[0] -= 1
                time[0] = '0'+time[0]
            }
        }
        else
        {
            time[1] -= 1

            if (time[1] < 10) time[1] = '0'+time[1]
        }

        time = time[0]+':'+time[1]

        timer.innerHTML = time

        if (time != '00:00') setTimeout(DocsVer.countdown, 1000)
    }
}

setTimeout(DocsVer.countdown, 1000)
