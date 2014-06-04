#!/usr/bin/env groovy

import java.io.FileWriter
import java.io.File
import groovy.xml.MarkupBuilder

def WIDTH = 400
def HEIGHT = 400
def BLUR = 0.3
def APPLE_RADIUS = 80
def EYELID_INNER_RADIUS = 130
def EYELID_OUTER_RADIUS = 180

def CX = WIDTH / 2
def CY = HEIGHT / 2 + (HEIGHT - (EYELID_OUTER_RADIUS + APPLE_RADIUS)) / 2

def writer = new FileWriter(new File("melkor.svg"))
def result = new MarkupBuilder(writer)

result.doubleQuotes = true
result.pi('xml':['version':'1.0', 'encoding':'UTF-8'])
result.yield('<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\n', false)
result.svg('xmlns':'http://www.w3.org/2000/svg', 'xmlns:xlink':'http://www.w3.org/1999/xlink', 'version':'1.1', 'width':"100%", 'height':"100%", 'viewBox':"0 0 $WIDTH $HEIGHT") {
	result.filter('id':'blur') {
		result.feGaussianBlur('stdDeviation': "$BLUR")
	}
	result.style('type':'text/css',"""
		.black {
			fill: #000000;
			filter:url(#blur);
		}
	""")
	result.circle('cx':"$CX", 'cy':"$CY", 'r':"$APPLE_RADIUS", 'class':'black')
	result.path('d':"M${CX - EYELID_INNER_RADIUS},${CY}a$EYELID_INNER_RADIUS,$EYELID_INNER_RADIUS,0,0,1,${2 * EYELID_INNER_RADIUS},0" +
					"m${EYELID_OUTER_RADIUS - EYELID_INNER_RADIUS},0a$EYELID_OUTER_RADIUS,$EYELID_OUTER_RADIUS,0,0,0,${-2 * EYELID_OUTER_RADIUS},0z",
				'class':'black')
}

writer = new FileWriter(new File("melkor_inverted.svg"))
result = new MarkupBuilder(writer)

result.doubleQuotes = true
result.pi('xml':['version':'1.0', 'encoding':'UTF-8'])
result.yield('<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">\n', false)
result.svg('xmlns':'http://www.w3.org/2000/svg', 'xmlns:xlink':'http://www.w3.org/1999/xlink', 'version':'1.1', 'width':"100%", 'height':"100%", 'viewBox':"0 0 $WIDTH $HEIGHT", 'style':'background-color : #000000') {
	result.filter('id':'blur') {
		result.feGaussianBlur('stdDeviation': "$BLUR")
	}
	result.style('type':'text/css',"""
		.white {
			fill: #FFFFFF;
			filter:url(#blur);
		}
	""")
	result.circle('cx':"$CX", 'cy':"$CY", 'r':"$APPLE_RADIUS", 'class':'white')
	result.path('d':"M${CX - EYELID_INNER_RADIUS},${CY}a$EYELID_INNER_RADIUS,$EYELID_INNER_RADIUS,0,0,1,${2 * EYELID_INNER_RADIUS},0" +
					"m${EYELID_OUTER_RADIUS - EYELID_INNER_RADIUS},0a$EYELID_OUTER_RADIUS,$EYELID_OUTER_RADIUS,0,0,0,${-2 * EYELID_OUTER_RADIUS},0z",
				'class':'white')
}