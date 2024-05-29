window.onload = async function () {

    

    // Notif fading
    const msg = document.getElementById('msg')
    if(msg != null) setTimeout(() => msg.style.opacity = '0', 3000)
    

    
    // Fetch and store cdt
    let teamCountPerDT = []
    let playerCountPerDT = []

    // Get Team & Player Stats
    await fetch('stats-api.php?tourna_id=13&req=team-player-dt')
        .then(res => res.json())
        .then(data => {
            teamCountPerDT = data.team
            playerCountPerDT = data.player
        })
    .catch(err => console.log(err))
    
    // Sort by cdt
    teamCountPerDT.sort((a, b) => { return (new Date(a.team_cdt)) - (new Date(b.team_cdt)) })
    playerCountPerDT.sort((a, b) => { return (new Date(a.player_cdt)) - (new Date(b.player_cdt)) })
    
    // Get Min Max 
    const currDT = new Date(Date.now())
    const teamMinDT = teamCountPerDT.length == 0? currDT : new Date(teamCountPerDT[0].team_cdt)
    const teamMaxDT = teamCountPerDT.length == 0? currDT : new Date(teamCountPerDT[teamCountPerDT.length - 1].team_cdt)
    const playerMinDT = playerCountPerDT.length == 0? currDT : new Date(playerCountPerDT[0].player_cdt)
    const playerMaxDT = playerCountPerDT.length == 0 ? currDT : new Date(playerCountPerDT[playerCountPerDT.length - 1].player_cdt)
    const minDT = teamMinDT < playerMinDT ? teamMinDT : playerMinDT
    const maxDT = teamMaxDT < playerMaxDT ? teamMaxDT : playerMaxDT

    // Generate date labels incrementing days
    let dtLbl = []
    let i = minDT.getTime();
    for (; i < maxDT.getTime(); i += 86400000) {   // increments 24hrs | 1day
        dtLbl.push(new Date(i))
    }

    // Get data from team & player
    let teamData = []
    let totalTeams = 0
    teamCountPerDT.forEach(t => {
        totalTeams += t.team_count
        teamData.push({ x: t.team_cdt, y: totalTeams })
    })

    let playerData = []
    let totalPlayers = 0
    playerCountPerDT.forEach(p => {
        totalPlayers += p.player_count
        playerData.push({ x: p.player_cdt, y: totalPlayers })
    })



    const data = {
        labels: dtLbl,
        datasets: [{
            label: 'Team Entry',
            backgroundColor: 'rgba(0, 184, 147, 0.5)',
            borderColor: 'rgb(0, 184, 147)',
            fill: false,
            data: teamData
        }, {
            label: 'Player Entry',
            backgroundColor: 'rgba(135, 206, 250, 0.5)',
            borderColor: 'lightskyblue',
            fill: false,
            data: playerData
        }]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            plugins: {
                title: {
                    text: 'Team and Player Entry Over Time',
                    color: 'white',
                    font: {
                        size: '16px'
                    },
                    display: true
                },
                legend: {
                    labels: {
                        color: 'white'
                    }
                }
            },
            scales: {
                x: {
                    type: 'time',
                    time: {
                        tooltipFormat: 'MM-dd-yyyy hh:mm a'
                    },
                    title: {
                        display: true,
                        text: 'Date',
                        color: 'white'
                    },
                    ticks: {
                        color: 'white'
                    },
                    grid: {
                        color: 'gray'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Team/Player Count',
                        color: 'white'
                    },
                    ticks: {
                        color: 'white'
                    },
                    grid: {
                        color: 'gray'
                    }
                }
            }
        }
    };

    // Get the context of the canvas element we want to select
    const ctx = document.getElementById('myChart').getContext('2d');

    // Create a new Chart instance
    new Chart(ctx, config);

    
    



}