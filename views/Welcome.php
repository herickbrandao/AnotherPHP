<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>Another - PHP8</title>
  <style type="text/css">
  	body {
  		margin: 0;
  		font-family: Arial;
  	}
  	#app {
  		width: 100%;
  		height: 100vh;
  		background: lightblue;
  	}
  	h1 {
  		position: absolute;
  		bottom: 20%;
  		width: 100%;
  		text-align: center;
  		font-weight: normal;
  	}
  	#infinite {
  		position: absolute;
  		display: flex;
  		align-items: center;
  		justify-content: center;
  		font-size: 200px;
  		height: 100%;
  		width: 100%;
  	}
  	#infinite span {
  		animation: twist 8s infinite linear;
  	}
  	@keyframes twist {
	  0%   {transform: rotate(0deg);}
	  100%  {transform: rotate(360deg);}
	}
  </style>
</head>
<body> 
  <main id="app">
  	<div id="infinite"><span>8</span></div>
  	<h1>Your <strong>Another</strong> is Running<br><small style="font-size: 16px;">Framework for PHP8</small></h1>
  </main>
</body>
</html>