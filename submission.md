TripIQ Interview Solution:

SceneA Solution:

   I have created CalculatorSceneA class to solve the SceneA Solution.
   Also tested using './vendor/bin/phpunit' command from root directory,
   and got all test cases succeeded (100%) for SceneA.

SceneB Solution:

   I have created CalculatorSceneB class to solve the SceneB Solution.
   Also tested using './vendor/bin/phpunit' command from root directory,
   and got all test cases succeeded (100%) for SceneB.

SceneC Solution:

   I have created CalculatorSceneC class to solve the SceneC Solution.
   Also tested using './vendor/bin/phpunit' command from root directory,
   and all the test cases were succeeded but except the last two cases for SceneC.(71.43%)

[NB : I think, the result of last two cases for SceneC might be wrong in the CalculatorTest file. I want to explain my solution according to the condition of SceneC.

test cases for SceneC: 
1) input: (Considering Friday 20:00-00:00 as weekday & Saturday 00:00-6:00 as Weekend)
   Start time-> 2016-05-13 20:00 ,
   End time-> 2016-05-14 06:00,
   Calculation : (13th May Friday) 8pm-12am will cost 1600p,
                 (14th May Saturday) 12am-6am will cost 1200p
   So, total cost by time is 1600+1200 = 2800p. But the test case expected 1600p.
2) input: (Considering Friday 18:00-00:00 as weekday & Saturday 00:00-4:00 as Weekend)
   Start time-> 2016-05-13 18:00 , 
   End time-> 2016-05-14 04:00 ,
   Calculation : (13th May Friday) 6pm-7pm will cost 665p,(£6.65 an hour between 7am and 7pm on weekdays)
                                   7pm-9pm will cost 2*400 = 800p,(£4.00 an hour outside of 7am to 7pm on weekdays)
                                   9pm-12am will cost 3*400 = 1200p, (£12.00 max between 9pm and 6am on weekdays)
               (14th May Saturday) 12am-4am will cost 4*200 = 800p,(£2.00 an hour during weekends)         
   So, total cost by time is 665+800+1200+800 = 3465p. But the test case expected 2400p.]

Extra Packages : No extra packages has been installed.