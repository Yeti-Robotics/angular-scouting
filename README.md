# Scouting Site To-Do list

- [X] Update form!!!
- [ ] ~~Make another php page that aggregates the details of each match from our data~~ (not required by Word of Mitch, chapter Stronghold, week 5)
- [ ] Display team's current ranking in a competition on [team.html](html/team.html) (retrieved from FRC servers)
- [x] Display team's name on [team.html](html/team.html) (retrieved from FRC servers)
- [x] Design wager system w/ sign in number & password
  - [x] Store session tokens in database to stay logged in on reload
  - [x] Update people's wagers after a match ends, e.g. when a stand scouting form is submitted
  - [x] Fix [getByteCoins.php](php/getByteCoins.php) returning an error when [TheCasino.html](html/TheCasino.html) is loaded
  - [x] Ensure wagers are only made for future matches by using the FRC API
- [x] Submit multiple pictures at once
- [ ] Individual user pages
  - [ ] Allow people to edit scouting form data to fix typos
  - [ ] Show people's past and pending wagers 
